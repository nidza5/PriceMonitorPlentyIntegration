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