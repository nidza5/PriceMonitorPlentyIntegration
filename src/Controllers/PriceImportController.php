<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\ConfigRepository;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;
 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
 use PriceMonitorPlentyIntegration\Services\ScheduleExportService;
 use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\PriceMonitorQueueRepository;
 use PriceMonitorPlentyIntegration\Constants\QueueType;
 use PriceMonitorPlentyIntegration\Contracts\RunnerTokenRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\RunnerTokenRepository;
 use PriceMonitorPlentyIntegration\Helper\StringUtils;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use Plenty\Modules\Helper\Services\WebstoreHelper;
 use PriceMonitorPlentyIntegration\Constants\ApiResponse;

 /**
  * Class PriceImportController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class PriceImportController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

         /**
         *
         * @var ScheduleRepositoryContract
         */
        private $scheduleRepo;

        /**
         *
         * @var ContractRepositoryContract
         */
        private $contractRepo;

         /**
         *
         * @var PriceMonitorQueueRepositoryContract
         */
        private $queueRepo;

          /**
         *
         * @var RunnerTokenRepositoryContract
         */
        private $tokenRepo;

        /**
         *
         * @var ConfigRepository
         */
        private $config;

        /**
         *
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;

     /**
     *
     * @var WebstoreHelper
     */
     private $webstoreHelper; 

    public function __construct(PriceMonitorSdkService $sdkService,ScheduleRepositoryContract $scheduleRepo,ContractRepositoryContract $contractRepo,PriceMonitorQueueRepositoryContract $queueRepo,RunnerTokenRepositoryContract $tokenRepo,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo,WebstoreHelper $webstoreHelper)
    {
        $this->sdkService = $sdkService;       
        $this->scheduleRepo = $scheduleRepo;      
        $this->contractRepo = $contractRepo;
        $this->queueRepo = $queueRepo;
        $this->tokenRepo = $tokenRepo; 
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;
        $this->webstoreHelper = $webstoreHelper;
    }


    public function saveSchedulePrices(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $priceMonitorId = $requestData['pricemonitorId'];
       
        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");
 
        $isEnabled = $requestData['enableImport'];

        $savePriceSchedule =  $this->sdkService->call("saveSchedulePriceToMiddleware", [
            'pricemonitorId' => $priceMonitorId,
            'enableImport' => $isEnabled      
        ]); 

        return json_encode($savePriceSchedule);

        // $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        // if($contract == null)
        //     throw new \Exception("Contract is empty");

        // if ($isEnabled && !$this->registerCallbacks($contract)) {
        //     throw new \Exception(ApiResponse::PRICE_IMPORT_UNABLE_TO_REGISTER_CALLBACKS);
        // }    
        
        //  $this->scheduleRepo->saveImportSchedule($contract->id,$requestData);

        //  $scheduleSaved = $this->scheduleRepo->getScheduleByContractId($contract->id);

        //  return json_encode($scheduleSaved);        
    }

    public function runPriceImport(Request $request) 
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $priceMonitorId = $requestData['pricemonitorId'];

        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");

        $queue = $this->queueRepo->getQueueByName(QueueType::DEFAULT_QUEUE_NAME);

        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');

        $emailForConfig = $emailObject->value;
        $passwordForConfig = $passwordObject->value;

       
        $enqueAndRun =  $this->sdkService->call("enqueuePriceImport", [
            'priceMonitorId' => $priceMonitorId,
            'queueModel' => $queue,
            'emailForConfig' =>  $emailForConfig,
            'passwordForConfig' =>  $passwordForConfig        
        ]); 

        if($enqueAndRun != null && $enqueAndRun['Message'])
        {
            return [
                'Message' => $enqueAndRun['Message']
            ];
        }

        if($enqueAndRun != null)
            $this->queueRepo->savePriceMonitorQueue($enqueAndRun['queueName'],$enqueAndRun['storageModel']);

        $createToken =  $this->sdkService->call("runAsyncWithToken", ['queueModel' => $queue]);   
      
        if($createToken != null && $createToken['error'])
           throw new \Exception($createToken['error_msg']);
        
        if($createToken != null &&  $createToken['isCreateRunnerToken'] == true)
        {
           $hashUniqueToken =  StringUtils::getUniqueString(20);    

           $savedToken = $this->tokenRepo->saveRunnerToken($hashUniqueToken);
 
           $returnValues = [
               "token" => $savedToken,
               "queueName" => $enqueAndRun['queueName']
           ];
            // call async
            return json_encode($returnValues);
        }

        return json_encode("OK");

    }

    public function registerCallbacks($contract)
    {
        $webHookToken = $this->configInfoRepo->getConfig('webhook_token');
        $tokenForSend = $webHookToken->value;

        $webstoreHelper = pluginApp(\Plenty\Modules\Helper\Services\WebstoreHelper::class);
        /** @var \Plenty\Modules\System\Models\WebstoreConfiguration $webstoreConfig */
        $webstoreConfig = $webstoreHelper->getCurrentWebstoreConfiguration();
        $callBackPrices =  $this->sdkService->call('registerCallBackForPrices', [
            'token' => $tokenForSend,
            'url' => $webstoreConfig->domainSsl . '/refreshPrices',
            'contract_Id' => $contract->id
        ]);
        
        return $callBackPrices;
    }

    public function refreshPrices() {

        echo "Uslo u refresh Prices";

        throw new \Exception("Nikola Vasiljevic - izvestaj da je uslo u metodu!");
    }

 }