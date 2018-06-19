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
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
 use PriceMonitorPlentyIntegration\Contracts\TransactionDetailsRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\TransactionDetailsRepository;
 use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\TransactionHistoryRepository;
 use PriceMonitorPlentyIntegration\Constants\FilterType;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;

 /**
  * Class TransactionHistoryController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class TransactionHistoryController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

         /**
         *
         * @var ContractRepositoryContract
         */
        private $contractRepo;

         /**
         *
         * @var TransactionDetailsRepositoryContract
         */
        private $transactionDetailsRepo;

        /**
         *
         * @var TransactionHistoryRepositoryContract
         */
        private $transactionHistoryRepo;

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

    public function __construct(PriceMonitorSdkService $sdkService,ContractRepositoryContract $contractRepo,TransactionDetailsRepositoryContract $transactionDetailsRepo,TransactionHistoryRepositoryContract $transactionHistoryRepo,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo)
    {
        $this->sdkService = $sdkService;       
        $this->contractRepo = $contractRepo;
        $this->transactionDetailsRepo = $transactionDetailsRepo;
        $this->transactionHistoryRepo = $transactionHistoryRepo;
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;
    }

    public function getTransactionHistory(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $masterId = $requestData['masterId'];
        $pricemonitorId = $requestData['pricemonitorId'];
        $limit = $requestData['limit'];
        $offset = $requestData['offset'];
        
        $detailed = $masterId !== null;

        $contract = $this->contractRepo->getContractByPriceMonitorId($pricemonitorId);

        if(!$contract)
            throw new \Exception("Contract is null");

        $transactionHistoryDetailsRecords = null;
        $totalDetailedRecords = 0;
        $transactionHistoryRecords = null;
        $totalHistoryRecords = 0;

        if($detailed) {
            $transactionHistoryDetailsRecords  = $this->transactionDetailsRepo->getAllTransactionDetails();
            $totalDetailedRecords = $this->transactionDetailsRepo->getTransactionHistoryDetailsCount($masterId);
        } else {
            $transactionHistoryRecords = $this->transactionHistoryRepo->getAllTransactionHistory();
            $totalHistoryRecords = $this->transactionHistoryRepo->getTransactionHistoryMasterCount($contract->id,FilterType::EXPORT_PRODUCTS);
        }

        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');

        $emailForConfig = $emailObject->value;
        $passwordForConfig = $passwordObject->value;

        $transactionHistoryAct =  $this->sdkService->call("getTransHistoryDetails", [
            'masterId' => $masterId,
            'pricemonitorId' => $pricemonitorId,
            'limit' => $limit,
            'offset' =>  $offset,
            'transactionHistoryDetailsRecord' => $transactionHistoryDetailsRecords,
            'totalDetailedRecords' => $totalDetailedRecords,
            'transactionHistoryRecords' => $transactionHistoryRecords,
            'totalHistoryRecords' => $totalHistoryRecords,
            'emailForConfig' =>  $emailForConfig,
            'passwordForConfig' =>  $passwordForConfig
        ]);   

        echo json_encode( $transactionHistoryAct);
        
        if($transactionHistoryAct != null && $transactionHistoryAct['error'])
           throw new \Exception($transactionHistoryAct['error_msg']);

        return json_encode($transactionHistoryAct);   
    }
 }