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
 use PriceMonitorPlentyIntegration\Contracts\RunnerTokenRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\RunnerTokenRepository;
 use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\PriceMonitorQueueRepository;
 use PriceMonitorPlentyIntegration\Constants\QueueType;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use PriceMonitorPlentyIntegration\Constants\FilterType;
 use PriceMonitorPlentyIntegration\Constants\TransactionStatus;
 use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
 use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\AttributesMappingRepository;
 use PriceMonitorPlentyIntegration\Services\ProductFilterService;
 use PriceMonitorPlentyIntegration\Services\AttributeService;
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
 use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\TransactionHistoryRepository;
 use  PriceMonitorPlentyIntegration\Contracts\TransactionDetailsRepositoryContract;
 use  PriceMonitorPlentyIntegration\Repositories\TransactionDetailsRepository;

 /**
  * Class SyncController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class SyncController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

        /**
         *
         * @var RunnerTokenRepositoryContract
         */
        private $tokenRepo;

        /**
         *
         * @var PriceMonitorQueueRepositoryContract
         */
        private $queueRepo;

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
         * @var ProductFilterRepository
         */
        private $productFilterRepo;

        /**
         *
         * @var AttributesMappingRepositoryContract
         */
        private $attributesMappingRepo;

        /**
         *
         * @var ContractRepositoryContract
         */        
        private $contractRepo;

        /**
         *
         * @var TransactionHistoryRepositoryContract
         */
        private $transactionHistoryRepo;

       
        /**
         *
         * @var  TransactionDetailsRepositoryContract
         */
        private $transactionDetailsHistoryRepo;

    public function __construct(PriceMonitorSdkService $sdkService,RunnerTokenRepositoryContract $tokenRepo,PriceMonitorQueueRepositoryContract $queueRepo,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo,ProductFilterRepositoryContract $productFilterRepo,AttributesMappingRepositoryContract $attributesMappingRepo,ContractRepositoryContract $contractRepo,TransactionHistoryRepositoryContract $transactionHistoryRepo,TransactionDetailsRepositoryContract $transactionDetailsHistoryRepo)
    {
        $this->sdkService = $sdkService;
        $this->tokenRepo = $tokenRepo;  
        $this->queueRepo = $queueRepo;
        $this->config = $config;   
        $this->configInfoRepo = $configInfoRepo;
        $this->productFilterRepo = $productFilterRepo;     
        $this->attributesMappingRepo = $attributesMappingRepo; 
        $this->contractRepo = $contractRepo;
        $this->transactionHistoryRepo = $transactionHistoryRepo; 
        $this->transactionDetailsHistoryRepo = $transactionDetailsHistoryRepo; 
    }

    
    public function run(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $queueName = $requestData['queueName'];

        if($queueName === "" || $queueName === null)
            throw new \Exception("queueName is empty");

        $token = $requestData['token'];

        if($token === "" || $token === null)
            throw new \Exception("token is empty");

        $priceMonitorId = $requestData['pricemonitorId'];

        $this->tokenRepo->deleteToken($token); 

     //   $this->queueRepo->deleteAllQueue();

        $queue = $this->queueRepo->getQueueByName($queueName);

        $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,FilterType::EXPORT_PRODUCTS);

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
            'filterType' => FilterType::EXPORT_PRODUCTS,
            'priceMonitorId' => $priceMonitorId,
            'productFilterRepo' => $filter,
            'attributeMapping' => $attributeMapping,
            'allVariations' =>  $allVariations,
            'attributesFromPlenty' => $attributesIdName            
        ]);     


            foreach( $filteredVariation as $r) {
                echo  json_encode($r);
            }
      
    //     $variationForExport = array();

    //     echo "arrayaaa";

    //    $f =  json_encode($filteredVariation);

    //    $b = json_decode($f);


    //    try {
    //     foreach($b as $v) {
           
    //         // echo json_encode($v);

    //         foreach($v as $r){
    //             try {
    //              echo $r["id"];
    //             } catch(\Exception $ex) {
    //                 echo $ex->getMessage();
    
    //        }
    //         }
    //     }

    //    } catch(\Exception $ex) {
    //             echo $ex->getMessage();

    //    }

     

        

    //    foreach ($filteredVariation as $p) {
    //         echo json_encode($p);
    // }

        // $emailObject = $this->configInfoRepo->getConfig('email');
        // $passwordObject = $this->configInfoRepo->getConfig('password');

        // $emailForConfig = $emailObject->value;
        // $passwordForConfig = $passwordObject->value;

        // $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        // //startTransaction

        // $startTransaction = $this->sdkService->call("startTransaction", [
        //     'contractId' => $priceMonitorId
        // ]);

        //   // echo json_encode($startTransaction['transactionHistoryMaster']);

        //  $savedTransactionMasterHistory =  $this->transactionHistoryRepo->saveTransactionHistoryMaster($startTransaction['transactionHistoryMaster']); 

        // //  $injectSaveTransactionHistory = $this->sdkService->call("injectSaveTransactionHistory", [ 
        // //         "savedTransactionMasterRecord" =>  $savedTransactionMasterHistory
        // //  ]);

        // $syncRun =  $this->sdkService->call("runSync", [
        //     'queueModel' => $queue,
        //     'queueName' => $queueName,
        //     'emailForConfig' =>  $emailForConfig,
        //     'passwordForConfig' =>  $passwordForConfig,
        //     'filterType' => FilterType::EXPORT_PRODUCTS,
        //     'priceMonitorId' => $priceMonitorId,
        //     'productFilterRepo' => $filter,
        //     'products' => $filteredVariation,
        //     'attributesFromPlenty' => $attributesIdName,
        //     'attributeMapping' => $attributeMapping,
        //     'contract' => $contract,
        //     "savedTransactionMasterRecord" =>  $savedTransactionMasterHistory              
        // ]);   

        // echo json_encode($syncRun);

        // if( $syncRun != null)
        // {
        //     foreach($syncRun['arrayUniqueIdentifier'] as $sync) {

        //         if($sync != null && $sync != -1) {
                    
        //             if($savedTransactionMasterHistory['id'] != null) {
        //                  $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,FilterType::EXPORT_PRODUCTS,$savedTransactionMasterHistory['id']);
        //                  $transactionHistoryDetailsForSaving = $syncRun['transactionHistoryDetailsForSaving'];
        //                  $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,$savedTransactionMasterHistory['id'],null,TransactionStatus::IN_PROGRESS);
                    
        //             } else {

        //                 $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,FilterType::EXPORT_PRODUCTS,null,$sync);
        //                 $transactionHistoryDetailsForSaving = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$sync,null);
        //                 $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$sync,TransactionStatus::IN_PROGRESS);
        //             }

        //             $this->transactionDetailsHistoryRepo->updateTransactionHistoryDetailsState($transactionHistoryDetailsForSaving, FilterType::EXPORT_PRODUCTS,$sync,null);
        //             $this->transactionHistoryRepo->updateTransactionHistoryMasterState($transactionHistoryMaster,$transactionHistoryDetailsForSaving,FilterType::EXPORT_PRODUCTS,$sync,$allTransactionsDetailsInProgress);
                    
        //             $enqueueStatusCheckerJob =  $this->sdkService->call("enqueueStatusCheckerJob", [
        //                 'queueModel' => $queue,
        //                 'exportTaskId' => $sync,
        //                 'contractId' => $contract->priceMonitorId             
        //             ]); 

        //             if($enqueueStatusCheckerJob != null && $enqueueStatusCheckerJob['Message'])
        //             {
        //                 return [
        //                     'Message' => $enqueueStatusCheckerJob['Message']
        //                 ];
        //             } 

        //             if($enqueueStatusCheckerJob != null)
        //                 $this->queueRepo->savePriceMonitorQueue($enqueueStatusCheckerJob['queueName'],$enqueueStatusCheckerJob['storageModel']);
        //         }             
        //     }

        //     foreach($syncRun['dequeus'] as $deq) 
        //       $this->queueRepo->deleteQueue($deq['QueueName'],$deq['StorageModel']);

        //     foreach($syncRun['release'] as $rel)
        //         $this->queueRepo->updateReservationTime($rel['QueueName'],$rel['StorageModel']);
        // }      
        
        // $result = ['successSync' => $syncRun];
        // return  json_encode($result);
    }
 }