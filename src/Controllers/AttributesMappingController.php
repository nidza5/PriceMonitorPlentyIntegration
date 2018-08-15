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

    public function __construct(PriceMonitorSdkService $sdkService)
    {
        $this->sdkService = $sdkService;       
    }

    public function getMappedAttributes(Request $request) :string 
    {
        $requestData = $request->all();
        $priceMonitorId = 0;

        if ($requestData != null) {
            $priceMonitorId = $requestData['priceMonitorContractId'];
        }            

        $getMappedAttributesFromMiddleware = $this->sdkService->call("getMappedAttributesFromMiddleware", [
            'priceMonitorContractId' => $priceMonitorId        
        ]);  

        return json_encode($getMappedAttributesFromMiddleware);     
    }

    public function saveAttributesMapping(Request $request)
    {
        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("Request data is empty");
        }           

        $priceMonitorId = $requestData['pricemonitorId'];
        $mappings = $requestData['mappings'];
       
        if ($priceMonitorId === 0 || $priceMonitorId === null) {
            throw new \Exception("PriceMonitorId is empty");
        }            

        if ($mappings == null) {
            throw new \Exception("Mappings is empty");
        }            
           
        $this->sdkService->call("saveAttributesMappingToMiddleware", [
            'priceMonitorId' => $priceMonitorId,
            'mappings' => $mappings         
        ]);    

         return "OK";        
    }
 }