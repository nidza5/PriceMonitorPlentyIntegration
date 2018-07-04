<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
use Plenty\Plugin\Http\Request;

class AttributeService {


     /**
     *
     * @var PropertyRepository
     */
    private $propertyRepository;

    /**
     *
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     *
     * @var AttributeValueRepository
     */
    private $attributeValueRepository;
     /**
     * Constructor.
     *
     * @param AttributeValueRepositoryContract $attributeValueRepo
     * @param PropertyRepositoryContract $propRepo
     * @param AttributeRepositoryContract $attributeRepo
     */
    public function __construct(PropertyRepositoryContract $propertyRepository,AttributeRepositoryContract $attributeRepository,AttributeValueRepositoryContract $attributeValueRepository)
    {
        $this->propertyRepository = $propertyRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeValueRepository = $attributeValueRepository;
    }

    public function getAllTypeAttributes()
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

       $systemAttr = [
            "VariationN" => "name",
            "VariationNo" => "number",
            "GTIN13" => "GTIN 13",
            "GTIN128" => "GTIN 128",
            "UPC" => "UPC barcode",
            "IBBN" => "ISBN barcode"
       ];      
       
        foreach($systemAttr as $key => $value)
        {
            $arrSystemAttributes = array(
                "Id" => $key,
                "Group" => "System attributes",
                "Name" => $value.'-string',
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
                "Name" => $other.'-string'  
         );  

            $dataAttributes[] = $arrOthers;
        }     

        foreach($dataAttributes as $arr){
            $finalResult[$arr["Group"]][$arr["Id"]]=$arr["Name"];
        }

        return $finalResult;
    }
}

?>