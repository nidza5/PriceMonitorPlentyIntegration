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
        $type = $requestData['type']
        
        
        $transactionFromMiddleware =  $this->sdkService->call("getTransactionHistoryFromMiddleware", [
            'pricemonitorId' => $pricemonitorId,
            'masterId' => $masterId,
            'type' => $type,
            'limit' => $limit,
            'offset' =>  $offset          
        ]);

        return json_encode($transactionFromMiddleware);   
    }
 }