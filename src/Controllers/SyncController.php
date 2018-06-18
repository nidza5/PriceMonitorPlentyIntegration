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


    public function __construct(PriceMonitorSdkService $sdkService,RunnerTokenRepositoryContract $tokenRepo,PriceMonitorQueueRepositoryContract $queueRepo)
    {
        $this->sdkService = $sdkService;
        $this->tokenRepo = $tokenRepo;  
        $this->queueRepo = $queueRepo;      
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

        $this->tokenRepo->deleteToken($token); 

        $queue = $this->queueRepo->getQueueByName($queueName);

        echo json_encode( $queue);

        $syncRun =  $this->sdkService->call("runSync", [
            'queueModel' => $queue,
            'queueName' => $queueName            
        ]);   
         
        $result = ['successSync' => $syncRun];
        return  json_encode($result);
    }
 }