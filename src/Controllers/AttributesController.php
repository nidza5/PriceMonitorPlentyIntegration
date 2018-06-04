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

        $attributesRepo = pluginApp(AttributeRepositoryContract::class);

        $authHelperAttr = pluginApp(AuthHelper::class);
        
        $attributes = null;

        $attributes = $authHelperAttr->processUnguarded(
          function () use ($attributesRepo, $attributes) {
          
              return $attributesRepo->all();
          }
      );

       $resultAttributes = $attributes->toArray();
        
       $dataAttributes = array();

       $systemAttr = array("Variation name","Variation No","GTIN 13 barcode","GTIN 128 barcode","UPC barcode","ISBN barcode");

       foreach($systemAttr as $nonAttr)
       {
          $arrSystemAttributes = array(
            "Id" => $nonAttr,
            "Group" => "System attributes",
            "Name" => $nonAttr.'-text',
       );       
       
            $dataAttributes[] = $arrSystemAttributes;
       }        
       
        foreach($resultAttributes['entries'] as $att) 
        {
            $arrNonSystemAttributes = array(
                 "Id" => $att['id'],
                 "Group" => "Non system attributes",
                 "Name" => $att['backendName'].'-'.$att['typeOfSelectionInOnlineStore'] 
            );
            
            $dataAttributes[] = $arrNonSystemAttributes;
        }


        $propertiesRepo = pluginApp(PropertyRepositoryContract::class);

        $authHelperProp = pluginApp(AuthHelper::class);

        $properties = null;

        $properties = $authHelperProp->processUnguarded(
          function () use ($propertiesRepo, $properties) {          
              return $propertiesRepo->all();
            }
        );

         $resultProperties = $properties->toArray();

         foreach($resultProperties['entries'] as $prop) 
         {
             $arrProperties = array(
                  "Id" => $prop['id'],
                  "Group" => "Properties",
                  "Name" => $prop['backendName'].'-'.$prop['valueType']
             );
             
             $dataAttributes[] = $arrProperties;
         }

         $othersArr = array("Category","Manufacturer","Supplier","Channel");

         foreach($othersArr as $other)
         {
            $arrOthers = array(
                "Id" => $other,
                "Group" => "Other",
                "Name" => $other.'-'.$other  
         );  

            $dataAttributes[] = $arrOthers;
        }     

        foreach($dataAttributes as $arr){
            $finalResult[$arr["Group"]][$arr["Id"]]=$arr["Name"];
        }

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