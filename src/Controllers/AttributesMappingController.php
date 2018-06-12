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


    public function __construct(PriceMonitorSdkService $sdkService,AttributesMappingRepositoryContract $attributesMappingRepo)
    {
        $this->sdkService = $sdkService;       
        $this->attributesMappingRepo = $attributesMappingRepo;      
    }



    public function getMappedAttributes(Request $request) :string 
    {
        $requestData = $request->all();
        $priceMonitorId = 0;

        if($requestData != null)
            $priceMonitorId = $requestData['priceMonitorContractId'];

        $attributeMapping = $this->attributesMappingRepo->getAttributeMappingByPriceMonitorId($priceMonitorId);    

        return json_encode($attributeMapping);     
    }

    public function saveAttributesMapping(Request $request)
    {
        return "OK"; 
    }
 }