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

    public function __construct(PriceMonitorSdkService $sdkService,ScheduleRepositoryContract $scheduleRepo,ContractRepositoryContract $contractRepo,PriceMonitorQueueRepositoryContract $queueRepo)
    {
        $this->sdkService = $sdkService;       
        $this->scheduleRepo = $scheduleRepo;      
        $this->contractRepo = $contractRepo;
        $this->queueRepo = $queueRepo;
    }

    public function getSchedule(Request $request) :string 
    {
        $requestData = $request->all();
        $priceMonitorId = 0;

        if($requestData != null)
            $priceMonitorId = $requestData['pricemonitorId'];

        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");

         /**
         * @var ScheduleExportService $scheduleExportService
         */
        $scheduleExportService = pluginApp(ScheduleExportService::class);

        $scheduleSaved = $scheduleExportService->getAdequateScheduleByContract($priceMonitorId);

        return json_encode($scheduleSaved);  
   
    }

    public function saveSchedule(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $priceMonitorId = $requestData['pricemonitorId'];
       
        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");
        
        $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        if($contract == null)
            throw new \Exception("Contract is empty");

         $this->scheduleRepo->saveSchedule($contract->id,$requestData);

         $scheduleSaved = $this->scheduleRepo->getScheduleByContractId($contract->id);

         return json_encode($scheduleSaved);        
    }

    public function runProductExport(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");

        $priceMonitorId = $requestData['pricemonitorId'];

        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");

        $queue = $this->queueRepo->getQueueByName(QueueType::DEFAULT_QUEUE_NAME);

        $enqueAndRun =  $this->sdkService->call("enqueueProductExport", [
            'priceMonitorId' => $priceMonitorId,
            'queueModel' => $queue            
        ]); 

        echo json_encode($enqueAndRun);
        

        return "OK";
    }
 }