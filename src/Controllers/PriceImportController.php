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
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use Plenty\Modules\Helper\Services\WebstoreHelper;

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

    public function __construct(PriceMonitorSdkService $sdkService,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo,WebstoreHelper $webstoreHelper)
    {
        $this->sdkService = $sdkService;         
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;
        $this->webstoreHelper = $webstoreHelper;
    }

    public function saveSchedulePrices(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }         

        $priceMonitorId = $requestData['pricemonitorId'];
       
        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }           

        $isEnabled = $requestData['enableImport'];

        $savePriceSchedule =  $this->sdkService->call("saveSchedulePriceToMiddleware", [
            'pricemonitorId' => $priceMonitorId,
            'enableImport' => $isEnabled      
        ]); 

        return json_encode($savePriceSchedule);       
    }

    public function runPriceImport(Request $request) 
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }           

        $priceMonitorId = $requestData['pricemonitorId'];

        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }            

        $runProductImport =  $this->sdkService->call("runPriceImportMiddleware", [
            'pricemonitorId' => $priceMonitorId      
        ]); 
    }
 }