<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\ConfigRepository;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;

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
         * @var ConfigRepository
         */
        private $config;

         /**
         *
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;

    public function __construct(PriceMonitorSdkService $sdkService, ConfigRepository $config, ConfigRepositoryContract $configInfoRepo)
    {
        $this->sdkService = $sdkService;       
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;
    }

    public function getTransactionHistory(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }           

        $masterId = $requestData['masterId'];
        $pricemonitorId = $requestData['pricemonitorId'];
        $limit = $requestData['limit'];
        $offset = $requestData['offset'];
        $type = $requestData['type'];        
        
        $transaction =  $this->sdkService->call("getTransactionHistoryFromMiddleware", [
            'pricemonitorId' => $pricemonitorId,
            'masterId' => $masterId,
            'type' => $type,
            'limit' => $limit,
            'offset' => $offset          
        ]);

        return json_encode($transaction);   
    }

    public function getLastTransactionHistory(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }           

        $pricemonitorId = $requestData['pricemonitorId'];
        $type = $requestData['type'];

        $transaction = $this->sdkService->call("getLastTransactionHistoryFromMiddleware", [
            'pricemonitorId' => $pricemonitorId,
            'type' => $type         
        ]);

        return json_encode($transaction); 
    }
 }