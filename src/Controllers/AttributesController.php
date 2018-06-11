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
 use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
 use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
 use PriceMonitorPlentyIntegration\Services\AttributeService;

 /**
  * Class AttributesController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class AttributesController extends Controller
 {
     use Loggable;

        /**
         *
         * @var ConfigRepository
         */
        private $config;

        /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

        /**
         *
         * @var AttributeRepositoryContract
         */
        private $attributeRepository;


    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,AttributeRepositoryContract $attributeRepository)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->attributeRepository = $attributeRepository;     
    }   

    public function getAttributes(Request $request) :string 
    {
        /**
         * @var AttributeService $attributeService
         */
        $attributeService = pluginApp(AttributeService::class);

        $finalResult = $attributeService->getAllTypeAttributes();

         return json_encode($finalResult);   

    }

    public function getAttributeValueByAttrId(Request $request) : string
    {
        $requestData = $request->all();

        $attributeId = 0;

        if($requestData != null)
            $attributeId = $requestData['attributeId'];

        $attributesRepo = pluginApp(AttributeValueRepositoryContract::class);

        $authHelperAttr = pluginApp(AuthHelper::class);
        
        $attributesValues = null;

        $attributesValues = $authHelperAttr->processUnguarded(
            function () use ($attributesRepo, $attributesValues,$attributeId) {                   
                
                return $attributesRepo->findByAttributeId($attributeId);
            }
        );

        $resultAttributes = $attributesValues->toArray();

        return json_encode($resultAttributes['entries']);
        
    }
 }