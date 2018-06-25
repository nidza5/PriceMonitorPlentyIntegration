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
 use PriceMonitorPlentyIntegration\Constants\ProductConst;
 use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
 use PriceMonitorPlentyIntegration\Services\PaymentService;

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

        $typeOfFilter = $requestData['filterType'];

        $this->tokenRepo->deleteToken($token); 

    //    $this->queueRepo->deleteAllQueue();

        $queue = $this->queueRepo->getQueueByName($queueName);

        // echo "queue";
        // echo json_encode( $queue);

        $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,$typeOfFilter);

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

        echo json_encode($syncRun);

        if( $syncRun != null)
        {
            if($typeOfFilter == FilterType::EXPORT_PRODUCTS) {

                foreach($syncRun['arrayUniqueIdentifier'] as $sync) {
                    
                    if($sync != null && $sync != -1) {
                        
                        $this->updateTransactionHistoryBasedOnFilterType($typeOfFilter,$contract,$savedTransactionMasterHistory['id'],$syncRun['transactionHistoryDetailsForSaving'],$sync); 
                        // if($savedTransactionMasterHistory['id'] != null) {
                        //      $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,FilterType::EXPORT_PRODUCTS,$savedTransactionMasterHistory['id']);
                        //      $transactionHistoryDetailsForSaving = $syncRun['transactionHistoryDetailsForSaving'];
                        //      $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,$savedTransactionMasterHistory['id'],null,TransactionStatus::IN_PROGRESS);
                        
                        // } else {

                        //     $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,FilterType::EXPORT_PRODUCTS,null,$sync);
                        //     $transactionHistoryDetailsForSaving = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$sync,null);
                        //     $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$sync,TransactionStatus::IN_PROGRESS);
                        // }

                        // $this->transactionDetailsHistoryRepo->updateTransactionHistoryDetailsState($transactionHistoryDetailsForSaving, FilterType::EXPORT_PRODUCTS,$sync,null);
                        // $this->transactionHistoryRepo->updateTransactionHistoryMasterState($transactionHistoryMaster,$transactionHistoryDetailsForSaving,FilterType::EXPORT_PRODUCTS,$sync,$allTransactionsDetailsInProgress);
                        
                        $enqueueStatusCheckerJob =  $this->sdkService->call("enqueueStatusCheckerJob", [
                            'queueModel' => $queue,
                            'exportTaskId' => $sync,
                            'contractId' => $contract->priceMonitorId             
                        ]); 

                        if($enqueueStatusCheckerJob != null && $enqueueStatusCheckerJob['Message'])
                        {
                            return [
                                'Message' => $enqueueStatusCheckerJob['Message']
                            ];
                        } 

                        if($enqueueStatusCheckerJob != null)
                            $this->queueRepo->savePriceMonitorQueue($enqueueStatusCheckerJob['queueName'],$enqueueStatusCheckerJob['storageModel']);
                    }
                }  
            } else if($typeOfFilter == FilterType::IMPORT_PRICES) {
                $transactionDetails = $this->updateTransactionHistoryBasedOnFilterType($typeOfFilter,$contract,$savedTransactionMasterHistory['id'],$syncRun['transactionHistoryDetailsForSaving'],null); 
                $batchNotImportedPrices = $this->createNotImportedPrices($syncRun['arrayUniqueIdentifier'], $transactionDetails,$filteredVariation,$allVariations,$typeOfFilter,$priceMonitorId,$attributesIdName,$attributeMapping);
            }  

            foreach($syncRun['dequeus'] as $deq) 
              $this->queueRepo->deleteQueue($deq['QueueName'],$deq['StorageModel']);

            foreach($syncRun['release'] as $rel)
                $this->queueRepo->updateReservationTime($rel['QueueName'],$rel['StorageModel']);
        }      
        
        $result = ['successSync' => $syncRun];
        return  json_encode($result);
    }

    public function createNotImportedPrices($prices, &$transactionDetails,$filteredVariation,$allVariations,$typeOfFilter,$priceMonitorId,$attributesIdName,$attributeMapping)
    {
        $filterPricesStart = 0;
        $filterPricesBatchSize = 10;
        $notImportedPrices = [];
        $productIdentifierCode = ProductConst::PRODUCT_IDENTIFIER;

        do {
            $batchPrices = array_slice($prices, $filterPricesStart, $filterPricesBatchSize);
            $filteredShopProducts = [];

            $filteredShopProducts = $this->getVariationsFromCollection($filteredVariation, $batchPrices); 
            
            $allProducts = $this->getVariationsFromCollection($allVariations, $batchPrices); 

            $batchPrices =  $this->sdkService->call("fillBatchPricesEmptyFields", [
                'allProducts' => $allProducts,
                'batchPrices' => $batchPrices,
                'filterType' => $typeOfFilter,
                'priceMonitorId' => $priceMonitorId,
                'attributesFromPlenty' => $attributesIdName,
                'attributeMapping' => $attributeMapping        
            ]); 

            if (count($filteredShopProducts) === 0) {
                foreach ($batchPrices as $batchPrice) {
                    $notImportedPrices[] = [
                        'productId' => $batchPrice['identifier'],
                        'errors' => [],
                        'name' => $batchPrice['name'],
                        'status' => TransactionStatus::FILTERED_OUT
                    ];
                }
            } else {

                $notImportedFilteredPrices = $this->sdkService->call("createNotImportedFilteredPrices", [
                    'filteredShopProducts' => $filteredShopProducts,
                    'transactionDetails' => $transactionDetails,
                    'batchPrices' => $batchPrices,
                    'filterType' => $typeOfFilter,
                    'priceMonitorId' => $priceMonitorId,
                    'attributesFromPlenty' => $attributesIdName,
                    'attributeMapping' => $attributeMapping        
                ]); 

                $notImportedPrices = array_merge(
                    $notImportedPrices,
                    $notImportedFilteredPrices
                );
            }

            $batchPrices = $this->removeFilteredPricesFromBatch($batchPrices, $notImportedPrices);

            $notImportedPrices = array_merge(
                $notImportedPrices,
                $this->updatePrices($priceMonitorId, $batchPrices)
            );
            $filterPricesStart += $filterPricesBatchSize;

        } while ($filterPricesStart < count($prices));

        return $notImportedPrices;

    }

    private function updatePrices($contractId, $batchPrices) {
        
        $systemCurrency = "EUR";
        $paymentService =  pluginApp(PaymentService::class);
        $allpayments = $paymentService->getAllPayment();

        if($allpayments != null) {
            if($allpayments[0] != null)
               $systemCurrency = $allpayments[0]['currency'];
        }

        foreach($batchPrices as $price) {
            if($price['currency'] ==  $systemCurrency) {
                
            }
        }
    }

    private function removeFilteredPricesFromBatch($batchPrices, $notImportedPrices)
    {
        foreach ($batchPrices as $batchPriceIndex => $batchPrice) {
            foreach ($notImportedPrices as $notImportedPrice) {
                if ($batchPrice['identifier'] == $notImportedPrice['productId']) {
                    unset($batchPrices[$batchPriceIndex]);
                    break;
                }
            }
        }

        return $batchPrices;
    }

    public function getVariationsFromCollection($inputVariations, $batchPrices) 
    {
        $results = array();
        foreach($batchPrices as $batchPrice) {

            $returnProducts = array_filter($inputVariations, function($value) use ($batchPrice) {
                    return $value["id"] == $batchPrice["id"];                 
            });

            array_push($results,$returnProducts);

        }

        return  $results;
    }

    public function updateTransactionHistoryBasedOnFilterType($filterType,$contract,$transactionId,$transactionHistoryDetails,$uniqueIdentifier) 
    {
        if($transactionId != null) {
            $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,$filterType,$transactionId);
            $transactionHistoryDetailsForSaving = $transactionHistoryDetails;
            $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,$transactionId,null,TransactionStatus::IN_PROGRESS);
       
       } else {

           $transactionHistoryMaster =  $this->transactionHistoryRepo->getTransactionHistoryMasterByCriteria($contract->priceMonitorId,$filterType,null,$uniqueIdentifier);
           $transactionHistoryDetailsForSaving = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$uniqueIdentifier,null);
           $allTransactionsDetailsInProgress = $this->transactionDetailsHistoryRepo->getTransactionHistoryDetailsByFilters($contract->priceMonitorId,null,$uniqueIdentifier,TransactionStatus::IN_PROGRESS);
       }

        $transactionDetails =  $this->transactionDetailsHistoryRepo->updateTransactionHistoryDetailsState($transactionHistoryDetailsForSaving, $filterType,$uniqueIdentifier,null);
       $this->transactionHistoryRepo->updateTransactionHistoryMasterState($transactionHistoryMaster,$transactionHistoryDetailsForSaving,$filterType,$uniqueIdentifier,$allTransactionsDetailsInProgress);
   
       return $transactionDetails;
    }
 }