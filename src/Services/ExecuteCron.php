<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Cloud\Storage\Models\StorageObject;
use Plenty\Modules\Cron\Contracts\CronHandler;
use Plenty\Modules\Frontend\Factories\FrontendFactory;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;
use Plenty\Plugin\CachingRepository;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use PriceMonitorPlentyIntegration\Constants\FilterType;
use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\PriceMonitorQueueRepository;
use PriceMonitorPlentyIntegration\Services\ProductFilterService;
use PriceMonitorPlentyIntegration\Services\AttributeService;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\AttributesMappingRepository;
use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\TransactionHistoryRepository;

class ExecuteCron extends CronHandler
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
        private $scheduleRepository;

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
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;

        /**
         *
         * @var AttributesMappingRepositoryContract
         */
        private $attributesMappingRepo;

        /**
         *
         * @var ProductFilterRepository
         */
        private $productFilterRepo;

         /**
         *
         * @var TransactionHistoryRepositoryContract
         */
        private $transactionHistoryRepo;

    public function __construct(ScheduleRepositoryContract $scheduleRepository,ContractRepositoryContract $contractRepo,PriceMonitorQueueRepositoryContract $queueRepo,ConfigRepositoryContract $configInfoRepo,AttributesMappingRepositoryContract $attributesMappingRepo,PriceMonitorSdkService $sdkService,ProductFilterRepository $productFilterRepo,TransactionHistoryRepositoryContract $transactionHistoryRepo)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->contractRepo = $contractRepo;
        $this->queueRepo = $queueRepo;
        $this->configInfoRepo = $configInfoRepo; 
        $this->attributesMappingRepo = $attributesMappingRepo; 
        $this->sdkService = $sdkService;
        $this->productFilterRepo = $productFilterRepo;
        $this->transactionHistoryRepo = $transactionHistoryRepo;
    }

    /**
     * mandatory handle function
     */
    public function handle()
    {
        
        $queueName = "Default";

        $contracts = $this->contractRepo->getContracts(); 

        foreach($contracts as $contract) {
           
            $scheduleSaved =  $this->scheduleRepository->getScheduleByContractId($contract->id);

            if (!$scheduleSaved->enableExport) {
                continue;
            }
            
        $priceMonitorId = $contract->id;

        $typeOfFilter =  FilterType::EXPORT_PRODUCTS;

     //   $this->queueRepo->deleteAllQueue();

        $queue = $this->queueRepo->getQueueByName($queueName);

        $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,$typeOfFilter);

        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');

        $emailForConfig = $emailObject->value;
        $passwordForConfig = $passwordObject->value;

        $enqueAndRun =  $this->sdkService->call("enqueueProductExport", [
            'priceMonitorId' => $priceMonitorId,
            'queueModel' => $queue,
            'emailForConfig' =>  $emailForConfig,
            'passwordForConfig' =>  $passwordForConfig        
        ]); 

        if($enqueAndRun != null)
            $this->queueRepo->savePriceMonitorQueue($enqueAndRun['queueName'],$enqueAndRun['storageModel']);

        $attributeMapping = $this->attributesMappingRepo->getAttributeMappingCollectionByPriceMonitorId($priceMonitorId);    

        $itemService = pluginApp(ProductFilterService::class);

        $allVariations = $itemService->getAllVariations();

        // echo "variations";
        // return json_encode($allVariations);

        $attributeService = pluginApp(AttributeService::class);

        $attributesFromPlenty = $attributeService->getAllTypeAttributes();

        $attributesIdName = array();

        foreach($attributesFromPlenty as $key => $value) {
            foreach($value as $v => $l)
                $attributesIdName[$v] = explode("-",$l)[0];            

        }

        $filteredVariation =  $this->sdkService->call("getFilteredVariations", [
            'filterType' => $typeOfFilter,
            'priceMonitorId' => $priceMonitorId,
            'productFilterRepo' => $filter,
            'attributeMapping' => $attributeMapping,
            'allVariations' =>  $allVariations,
            'attributesFromPlenty' => $attributesIdName            
        ]);     


            // foreach( $filteredVariation as $r) {
            
            //     foreach($r as $p)
            //         echo $p["id"];
            
            // }
  

        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');

        $emailForConfig = $emailObject->value;
        $passwordForConfig = $passwordObject->value;

        $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        //startTransaction

        $startTransaction = $this->sdkService->call("startTransaction", [
            'contractId' => $priceMonitorId
        ]);

          // echo json_encode($startTransaction['transactionHistoryMaster']);

         $savedTransactionMasterHistory =  $this->transactionHistoryRepo->saveTransactionHistoryMaster($startTransaction['transactionHistoryMaster']); 

        //  $injectSaveTransactionHistory = $this->sdkService->call("injectSaveTransactionHistory", [ 
        //         "savedTransactionMasterRecord" =>  $savedTransactionMasterHistory
        //  ]);

        $syncRun =  $this->sdkService->call("runSync", [
            'queueModel' => $queue,
            'queueName' => $queueName,
            'emailForConfig' =>  $emailForConfig,
            'passwordForConfig' =>  $passwordForConfig,
            'filterType' => $typeOfFilter,
            'priceMonitorId' => $priceMonitorId,
            'productFilterRepo' => $filter,
            'products' => $filteredVariation,
            'attributesFromPlenty' => $attributesIdName,
            'attributeMapping' => $attributeMapping,
            'contract' => $contract,
            "savedTransactionMasterRecord" =>  $savedTransactionMasterHistory              
        ]);   


        }

        
    }
}