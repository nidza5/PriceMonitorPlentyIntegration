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
 use PriceMonitorPlentyIntegration\Constants\QueueType;
 use PriceMonitorPlentyIntegration\Helper\StringUtils;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use Plenty\Modules\Cron\Services\CronContainer;

 /**
  * Class ProductExportController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class ProductExportController extends Controller
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

        private $cronContainer;

    public function __construct(PriceMonitorSdkService $sdkService,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo,CronContainer $cronContainer)
    {
        $this->sdkService = $sdkService;           
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;   
        $this->cronContainer= $cronContainer;
    }

    public function getSchedule(Request $request) :string 
    {
        $requestData = $request->all();
        $priceMonitorId = 0;

        if ($requestData != null) {
            $priceMonitorId = $requestData['pricemonitorId'];
        }            

        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }       

        $getScheduleMiddleware = $this->sdkService->call("getScheduleFromMiddleware", [
            'pricemonitorId' => $priceMonitorId,     
        ]); 

        return json_encode($getScheduleMiddleware);    
    }

    public function saveSchedule(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }

        $priceMonitorId = $requestData['pricemonitorId'];
       
        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }            

        $startAt = $requestData['startAt'];
        $enableExport = $requestData['enableExport'];
        $exportInterval = $requestData['exportInterval'];

        $saveScheduleMiddleware = $this->sdkService->call("saveScheduleToMiddleware", [
            'pricemonitorId' => $priceMonitorId,
            'startAt' => $startAt,
            'enableExport' =>  $enableExport,
            'exportInterval' =>  $exportInterval        
        ]); 

        return json_encode($saveScheduleMiddleware);        
    }


    public function runProductExport(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data are empty!");
        }            

        $priceMonitorId = $requestData['pricemonitorId'];

        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }            

        $runProductExport =  $this->sdkService->call("runProductExportMiddleware", [
            'pricemonitorId' => $priceMonitorId      
        ]);     

        return $runProductExport;      
    }
 }