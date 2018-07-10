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
 use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\AttributesMappingRepository;
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;

 /**
  * Class AttributesMappingController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class AttributesMappingController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

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

    public function __construct(PriceMonitorSdkService $sdkService,AttributesMappingRepositoryContract $attributesMappingRepo,ContractRepositoryContract $contractRepo)
    {
        $this->sdkService = $sdkService;       
        $this->attributesMappingRepo = $attributesMappingRepo;      
        $this->contractRepo = $contractRepo;
    }



    public function getMappedAttributes(Request $request) :string 
    {
        $requestData = $request->all();
        $priceMonitorId = 0;

        if($requestData != null)
            $priceMonitorId = $requestData['priceMonitorContractId'];

     //   $attributeMapping = $this->attributesMappingRepo->getAttributeMappingCollectionByPriceMonitorId($priceMonitorId);    

        $getMappedAttributesFromMiddleware = $this->sdkService->call("getMappedAttributesFromMiddleware", [
            'priceMonitorContractId' => $priceMonitorId        
        ]);  

        return json_encode($getMappedAttributesFromMiddleware);     
    }

    public function saveAttributesMapping(Request $request)
    {
        $requestData = $request->all();

        if($requestData == null)
            return;

        $priceMonitorId = $requestData['pricemonitorId'];
        $mappings = $requestData['mappings'];

       
        if($priceMonitorId === 0 || $priceMonitorId === null)
            throw new \Exception("PriceMonitorId is empty");

        if($mappings == null)
            throw new \Exception("Mappings is empty");
        
        // $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        // if($contract == null)
        //     throw new \Exception("Contract is empty");

         
        $saveAttributesMappingToMiddleware =  $this->sdkService->call("saveAttributesMappingToMiddleware", [
            'priceMonitorId' => $priceMonitorId,
            'mappings' => $mappings         
        ]);    

       //  $this->attributesMappingRepo->saveAttributeMapping($contract->id,$contract->priceMonitorId,$mappings);

         return "OK";        
    }
 }