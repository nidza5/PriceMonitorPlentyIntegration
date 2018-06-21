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
 use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
 use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\AttributesMappingRepository;
 use PriceMonitorPlentyIntegration\Services\ProductFilterService;
 use PriceMonitorPlentyIntegration\Services\AttributeService;
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;

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


    public function __construct(PriceMonitorSdkService $sdkService,RunnerTokenRepositoryContract $tokenRepo,PriceMonitorQueueRepositoryContract $queueRepo,ConfigRepository $config,ConfigRepositoryContract $configInfoRepo,ProductFilterRepositoryContract $productFilterRepo,AttributesMappingRepositoryContract $attributesMappingRepo,ContractRepositoryContract $contractRepo)
    {
        $this->sdkService = $sdkService;
        $this->tokenRepo = $tokenRepo;  
        $this->queueRepo = $queueRepo;
        $this->config = $config;   
        $this->configInfoRepo = $configInfoRepo;
        $this->productFilterRepo = $productFilterRepo;     
        $this->attributesMappingRepo = $attributesMappingRepo; 
        $this->contractRepo = $contractRepo;
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

       
       // echo json_encode($filteredVariation);

        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');

        $emailForConfig = $emailObject->value;
        $passwordForConfig = $passwordObject->value;

        $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        $syncRun =  $this->sdkService->call("runSync", [
            'queueModel' => $queue,
            'queueName' => $queueName,
            'emailForConfig' =>  $emailForConfig,
            'passwordForConfig' =>  $passwordForConfig,
            'filterType' => FilterType::EXPORT_PRODUCTS,
            'priceMonitorId' => $priceMonitorId,
            'productFilterRepo' => $filter,
            'products' => $filteredVariation,
            'attributesFromPlenty' => $attributesIdName,
            'attributeMapping' => $attributeMapping,
            'contract' => $contract              
        ]);   
         
        $result = ['successSync' => $syncRun];
        return  json_encode($result);
    }
 }