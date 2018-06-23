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

    public function __construct(PriceMonitorSdkService $sdkService,ScheduleRepositoryContract $scheduleRepo,ContractRepositoryContract $contractRepo,PriceMonitorQueueRepositoryContract $queueRepo,RunnerTokenRepositoryContract $tokenRepo)
    {
        $this->sdkService = $sdkService;       
        $this->scheduleRepo = $scheduleRepo;      
        $this->contractRepo = $contractRepo;
        $this->queueRepo = $queueRepo;
        $this->tokenRepo = $tokenRepo; 
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
         
        $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        if($contract == null)
            throw new \Exception("Contract is empty");

         $this->scheduleRepo->saveImportSchedule($contract->id,$requestData);

         $scheduleSaved = $this->scheduleRepo->getScheduleByContractId($contract->id);

         return json_encode($scheduleSaved);        
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

 }