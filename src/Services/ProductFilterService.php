<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Item\DataLayer\Contracts\ItemDataLayerRepositoryContract;
use Plenty\Modules\Cloud\ElasticSearch\Lib\ElasticSearch;
use Plenty\Modules\Cloud\ElasticSearch\Lib\Processor\DocumentProcessor;
use Plenty\Modules\Cloud\ElasticSearch\Lib\Search\Document\DocumentSearch;
use Plenty\Modules\Item\Attribute\Contracts\AttributeNameRepositoryContract;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueNameRepositoryContract;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchSearchRepositoryContract;
use Plenty\Modules\Item\Search\Filter\CategoryFilter;
use Plenty\Modules\Item\Search\Filter\ClientFilter;
use Plenty\Modules\Item\Search\Filter\SearchFilter;
use Plenty\Modules\Item\Search\Filter\VariationBaseFilter;
use Plenty\Modules\Item\Variation\Contracts\VariationSearchRepositoryContract;
use Plenty\Modules\Item\Barcode\Contracts\BarcodeRepositoryContract;
use Plenty\Modules\Item\Barcode\Models\Barcode;
use Plenty\Modules\Category\Contracts\CategoryRepositoryContract;
use Plenty\Modules\Item\Variation\Contracts\VariationRepositoryContract;   
use Plenty\Modules\Item\Manufacturer\Contracts\ManufacturerRepositoryContract;
use Plenty\Modules\Item\VariationSupplier\Contracts\VariationSupplierRepositoryContract;
use Plenty\Modules\Account\Contact\Contracts\ContactRepositoryContract;
use Plenty\Modules\Account\Contact\Contracts\ContactAccountRepositoryContract;
use Plenty\Modules\Order\Referrer\Contracts\OrderReferrerRepositoryContract;
use PriceMonitorPlentyIntegration\Services\AttributeService;

class ProductFilterService {

      /**
     * Mandatory attributes
     *
     * @var array
     */
    public static $mandatoryAttributes = array(
        'gtin',
        'name',
        'referencePrice',
        'minPriceBoundary',
        'maxPriceBoundary'
    );

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
     *
     * @var ItemDataLayerRepositoryContract
     */
    private $itemDataLayerRepo;

    /**
     *
     * @var BarcodeRepositoryContract
     */
    private $barCodeRepository;
    
    

     /**
     * Constructor.
     *
     * @param AttributeValueRepositoryContract $attributeValueRepo
     * @param PropertyRepositoryContract $propRepo
     * @param AttributeRepositoryContract $attributeRepo
     */
    public function __construct(PropertyRepositoryContract $propertyRepository,AttributeRepositoryContract $attributeRepository,AttributeValueRepositoryContract $attributeValueRepository,ItemDataLayerRepositoryContract $itemDataLayerRepo,BarcodeRepositoryContract $barCodeRepository)
    {
        $this->propertyRepository = $propertyRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeValueRepository = $attributeValueRepository;
        $this->itemDataLayerRepo = $itemDataLayerRepo;
        $this->barCodeRepository = $barCodeRepository;
    }

    public function getAllVariations()
    {
        $repository = pluginApp(VariationSearchRepositoryContract::class);
        $barCodeRepo = pluginApp(BarcodeRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $repository->setSearchParams([
            'with' => [
                'variationAttributeValues' => null,
                'variationBarcodes' => 'barcode',
                'item' => ['itemProperties'],
                'variationCategories' => null,
                'variationSuppliers'  => null,
                'variationSalesPrices' => null,
                'variationMarkets' => 'name'
            ]
         ]);

        $products = $repository->search();

        $originalProducts = $products->getResult();

        $itemsResults = [];
        $i = 0;       

        foreach($originalProducts as $p) {
            $i++;
            $tempArr = null;            
            $itemsResults[$i] = $p;  

            if (!empty($p['variationBarcodes'])) {
                foreach($p['variationBarcodes'] as $bar) {               
                    $barCode = null;

                    $barCode = $authHelper->processUnguarded(
                        function () use ($barCodeRepo, $barCode,$bar) {
                        
                            return $barCodeRepo->findBarcodeById($bar['barcodeId']);
                        }
                    );

                    $barElement = [$barCode->name => $bar['code']];
    
                    $arrayForMerge = $tempArr == null ? $p : $tempArr;
                    $merge = array_merge($arrayForMerge,$barElement);  
                    $tempArr = $merge;              
                    $itemsResults[$i] = $merge;                 
                }
            }            
            
            foreach ($p['variationCategories'] as $category) {
                $categoryOriginal = $this->getCategoryById($category["categoryId"]);
                $categoryName =  $categoryOriginal != null ? $categoryOriginal[0]['details'][0]['name'] : "";

                $categoryElement = ["category-".$categoryName => $categoryName];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$categoryElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }

            foreach ($p['variationMarkets'] as $channel) {
                $channelOriginal = $this->getChannelById($channel['id']);
                $channelName =  $channelOriginal != null ? $channelOriginal['name'] : "";

                $channelElement = ["channel-".$channelName => $channelName];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$channelElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }

            foreach ($p['variationSuppliers'] as $supplier) {
                $supplierOriginal = $this->getSupplierById($supplier["supplierId"]);
                
                $supplierIdentifier = "";
                $supplierCompany = $supplierOriginal != null ? $supplierOriginal['accounts'][0]['companyName'] : "";

                if ($supplierCompany !== null &&  $supplierCompany !== "") {
                    $supplierIdentifier = $supplierCompany;
                }                   
                else {
                    $supplierIdentifier = $supplierOriginal != null ? $supplierOriginal['fullName'] : "";
                } 

                if ($supplierIdentifier === null ||  $supplierIdentifier === "") {
                    continue;
                }                       

                $supplierElement = ["supplier-".$supplierIdentifier => $supplierIdentifier];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$supplierElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }
            
            foreach ($p['variationAttributeValues'] as $attrinute) {
                $attributeName =  $attrinute["attribute"]["backendName"];
                $attributeValue =  $attrinute["attributeValue"]["backendName"];
            
                $attributeElement = [$attributeName => $attributeValue];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$attributeElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }

            foreach ($p['variationSalesPrices'] as $salesPrice) 
            {
                $priceId = $salesPrice['salesPriceId'];
                $priceValue = $salesPrice['price'];

                $priceElement = [$priceId.'-price' => $priceValue];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$priceElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }

            $itemWithProperties = $this->getItemWithPropertiesById($p["itemId"]);

            //add item text to main columns
            $itemText = "";
            if($itemWithProperties != null)                 
                $itemText = $itemWithProperties["texts"][0]['name1'];
            
            $itemTextElement = ["itemText" => $itemText];
            $arrayForMerge = $tempArr == null ? $p : $tempArr;
            $merge = array_merge($arrayForMerge,$itemTextElement);  
            $tempArr = $merge;              
            $itemsResults[$i] = $merge;    
           
            foreach ($itemWithProperties["itemProperties"] as $properties) {             
                $properyValue  = $properties["valueTexts"][0]["value"];
                if($properyValue == null)   {
                    if($properties["valueInt"] != null) 
                        $properyValue = $properties["valueInt"];
                        
                    if($properyValue == null && $properties["valueFloat"] != null)
                        $properyValue = $properties["valueFloat"];                           
                }

                $propertyId = "prop-".$properties["propertyId"];
                $propertyElement = [$propertyId => $properyValue];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$propertyElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }
            
            $manufacturerId = $p["item"]["manufacturerId"];
            $originalManufacturer = $this->getManufacturerById($manufacturerId);

            if($originalManufacturer != null) {
                $manufacturerElement = [ "manufacturer-".$originalManufacturer->name => $originalManufacturer->name];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$manufacturerElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }        
        }

           return $itemsResults;
    }

    public function getMappedAttributesCode($mappedAttribute) {
        $mappings = $mappedAttribute->toArray();
        $mappingCodes = array_unique(array_column($mappings, 'attribute_code'));
        $mappingCodes[] = 'tax_class_id';
        return $mappingCodes;
    }

    public function getChannelById($id)
    {
        $repoMarkets = pluginApp(OrderReferrerRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $market = null;

        $market = $authHelper->processUnguarded(
            function () use ($repoMarkets, $market) {
            
                return $repoMarkets->getReferrerById(110);
            }
        );

        return  $market;
    }

    public function getManufacturerById($id) 
    {
        $manufacturerRepo = pluginApp(ManufacturerRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $manufactures = null;

       $manufactures = $manufacturerRepo->findById($id);

        return $manufactures;
    }

    public function getSupplierById($id) {        

        $suppliersRepo = pluginApp(ContactRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $suppliers = null;

        $suppliers = $authHelper->processUnguarded(
            function () use ($suppliersRepo, $suppliers,$id) {
            
                return $suppliersRepo->findContactById($id);
            }
        );
   
        return $suppliers;
    }

    public function getItemWithPropertiesById($id)
    {        
        $itemRepo = pluginApp(ItemRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $item = null;

       $item = $itemRepo->show($id,["id","position"],"en",['itemProperties']);
       
        return $item;
    }

    public function getCategoryById($id) 
    {
        $categoriesRepo = pluginApp(CategoryRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $categories = null;

       $category = $categoriesRepo->search($id,1,50,["details" => null]);
       
       $finalCategory = $category->getResult();

        return $finalCategory;

    }

    public function getVariationById($id) {     
    
        $repository = pluginApp(VariationSearchRepositoryContract::class);
        $repository->setFilters([
            'id' => $id
        ]);

        $authHelper = pluginApp(AuthHelper::class);

        $repository->setSearchParams([
            'with' => [
                'variationSalesPrices' => null
            ]
         ]);

        $variation = $repository->search();

        $originalVariation = $variation->getResult();
        return $originalVariation;
    }

    public function addFilterByOperator($parentGroup,$groupOperator) 
    {
        try {
            $variationArray = $this->getAllVariations();

            $attributeService = pluginApp(AttributeService::class);
            
            $attributesFromPlenty = $attributeService->getAttributeForFilter();

            $finalFilteredProduct = array();
          
            $parentFilteredGroup = [];

            $filteredGroup = [];

            foreach($parentGroup as $group)
            {
               $filterVAriationByConditions = [];

                foreach($group["expressions"] as $exp) {
                    $operator = $exp['operator'];
                    $values = $exp['values'];
                    $attribute = $exp['attribute'];
                    $condition = $exp['condition'];

                    switch($attribute) {

                        case "Category" :
                            $filterByColumn = "category-".$values[0];
                        break;
                        case "Manufacturer" :
                             $filterByColumn = "manufacturer-".$values[0];
                        break;
                        case "Supplier" :
                              $filterByColumn = "supplier-".$values[0];
                        break;
                        case "Channel" :
                            $filterByColumn = "channel-".$values[0];
                            break;
                        case "id":
                            $filterByColumn = "id";
                            break;
                        default :
                            $filterByColumn = $attributesFromPlenty[$attribute]; 
                    }
        
                    $nameColumnInVariation = null;
    
                    switch($condition) {
    
                        case "equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "=",
                                                                "operator" =>  $operator];
                        break;
                        case "not_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "!=",
                                                                "operator" =>  $operator];
                        break;
                        case "greater_than" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => ">",
                                                                "operator" =>  $operator];
                        break;
                        case "less_than" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "<",
                                                                "operator" =>  $operator];
                        break;
                        case "greater_or_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => ">=",
                                                                "operator" =>  $operator];
                        break;
                        case "less_or_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "<=",
                                                                "operator" =>  $operator];
                        break;
                        case "contains" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "stripos!=",
                                                                "operator" =>  $operator];
                        break;
                        case "contains_not" :
                        $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                            "value" => $values[0],
                                                            "condition" => "stripos==",
                                                            "operator" =>  $operator];
                        break;
                    }
                }

                $filteredGroup['expressionFilter'] =  $filterVAriationByConditions;
                $filteredGroup['operator'] = $group["operator"];
                array_push($parentFilteredGroup,$filteredGroup);   
            } 

            $filteredProducts = array_filter($variationArray, function($value) use ($filterVAriationByConditions, $parentFilteredGroup) {
                $groupCondition = null;
                foreach($parentFilteredGroup as $filterGroup) 
                {
                    $condition = null;
                    foreach($filterGroup["expressionFilter"] as $variationCondition) {

                        $filterByCondition = $variationCondition["condition"];
                        
                        switch($filterByCondition) {    
                            case "=" :                               
                                    if($condition !== null) {
                                        if($variationCondition["operator"] === "AND")
                                        {
                                            if(isset($value[$variationCondition["filterByColumn"]])) 
                                                $condition = $condition && ($value[$variationCondition["filterByColumn"]] == $variationCondition["value"]);
                                            else 
                                                $condition = $condition &&  false;
                                        }
                                        else if($variationCondition["operator"] === "OR")
                                        {
                                            if(isset($value[$variationCondition["filterByColumn"]])) 
                                                $condition = $condition || $value[$variationCondition["filterByColumn"]] == $variationCondition["value"];
                                            else 
                                                $condition = $condition ||  false;

                                        }
                                    } else {                                      

                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition = ($value[$variationCondition["filterByColumn"]] == $variationCondition["value"]);
                                        else 
                                            $condition = false;   
                                    }
                                 
                               break;
                            case "!=" :                             
                                    if($condition !== null) {
                                        if($variationCondition["operator"] === "AND")
                                        {
                                            if(isset($value[$variationCondition["filterByColumn"]])) 
                                                $condition = $condition && $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                                            else 
                                                $condition = $condition && false;
                                        }
                                        else if($variationCondition["operator"] == "OR")
                                        {
                                            if(isset($value[$variationCondition["filterByColumn"]])) 
                                                $condition = $condition || $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                                            else 
                                                $condition = $condition ||  false;
                                        }
                                    } else {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition = $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                                        else 
                                            $condition = false;  
                                    }
                                
                            break;
                            case ">" :                           
                               if($condition !== null) {
                                 if($variationCondition["operator"] == "AND")
                                 {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = $condition && $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                                    else 
                                        $condition = $condition &&  false;
                                 }
                                 else if($variationCondition["operator"] == "OR")
                                 {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                                    else 
                                        $condition = $condition || false;
                                 }
                                } else {
                                    if(isset($value[$variationCondition["filterByColumn"]]))
                                         $condition =  $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                                    else 
                                         $condition = false; 
                                }
                            
                            break;
                            case "<" :                           
                              if($condition !== null) {
                                  if($variationCondition["operator"] == "AND")
                                  {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                      $condition = $condition && $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                                    else 
                                      $condition = $condition &&  false;
                                  }
                                  else if($variationCondition["operator"] == "OR")
                                  {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                                    else 
                                        $condition = $condition ||  false;
                                  }
                               } else {
                                if(isset($value[$variationCondition["filterByColumn"]]))
                                    $condition = $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                                else 
                                    $condition = false; 
                               }
                             
                              break;
                            case ">=" :                            
                                if($condition !== null) {
                                    if($variationCondition["operator"] == "AND")
                                    {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition = $condition && $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                        else 
                                            $condition = $condition &&  false;
                                    }
                                   else if($variationCondition["operator"] == "OR")
                                   {
                                      if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                       else 
                                        $condition = $condition ||  false;
                                   }
                                } else {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                         $condition = $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                    else 
                                         $condition = false; 
                                }
                            
                            break;
                            case "<=" :                           
                                if($condition !== null) {
                                    if($variationCondition["operator"] == "AND")
                                    {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition = $condition && $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                        else 
                                            $condition = $condition &&  false;
                                    }
                                    else if($variationCondition["operator"] == "OR")
                                    {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition = $condition || $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                        else 
                                            $condition = $condition ||  false;
                                    }
                                  } else {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                    else 
                                        $condition = false;
                                    }
                            
                             break;
                            case "stripos!=" :                           
                                if($condition !== null) {                                
                                    if($variationCondition["operator"] == "AND")
                                    {   
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                        else 
                                            $condition = $condition &&  false;
                                    }
                                    else if($variationCondition["operator"] == "OR")
                                    {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                        else 
                                            $condition = $condition ||  false;
                                    }
                                } else {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition = (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                    else 
                                        $condition = false;  
                                }
                             
                              break;
                            case "stripos==" :                           
                                if($condition !== null) {
                                    if($variationCondition["operator"] === "AND")
                                    {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                        else 
                                            $condition = $condition &&  false;
                                    }
                                     else if($variationCondition["operator"] === "OR")
                                     {
                                        if(isset($value[$variationCondition["filterByColumn"]])) 
                                            $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                        else 
                                            $condition = $condition ||  false;
                                     }
                                } else {
                                    if(isset($value[$variationCondition["filterByColumn"]])) 
                                        $condition =  (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                    else 
                                        $condition = false; 
                                }
                            
                            break;
                        }
                    }
                   
                    $operatorGroup = $filterGroup["operator"];
 
                    if($operatorGroup == "AND")
                        $groupCondition = $groupCondition === null ? ($condition) : $groupCondition && ($condition);
                    else if($operatorGroup == "OR")
                        $groupCondition = $groupCondition === null ? ($condition) : $groupCondition || ($condition);

                }                

                return $groupCondition;                    
            });
    
             return  array_values($filteredProducts);

        } catch (\Exception $ex)
        {
            $response = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage(),
                'Line' => $ex->getLine()
             ];

             return $response;

        }      
    }
}

?>