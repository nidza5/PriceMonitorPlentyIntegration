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
        // if(hasMandatoryMappings($mappedAttributes)) {
        //     throw new \Exception("Mandatory fields must be mapped!");
        // }

        $repository = pluginApp(VariationSearchRepositoryContract::class);
        $barCodeRepo = pluginApp(BarcodeRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $repository->setSearchParams([
            'with' => [
                'variationAttributeValues' => null,
                'variationBarcodes' => 'barcode',
                'item' => ['itemProperties'],
                'variationCategories' => null,
                'variationSuppliers'  => null
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
            
            foreach($p['variationCategories'] as $category) {
                $categoryOriginal = $this->getCategoryById($category["categoryId"]);
                $categoryName =  $categoryOriginal != null ? $categoryOriginal[0]['details'][0]['name'] : "";

                $categoryElement = ["category-".$categoryName => $categoryName];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$categoryElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }
            
            foreach($p['variationAttributeValues'] as $attrinute) {
                $attributeName =  $attrinute["attribute"]["backendName"];
                $attributeValue =  $attrinute["attributeValue"]["backendName"];
            
                $attributeElement = [$attributeName => $attributeValue];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$attributeElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }

            $itemWithProperties = $this->getItemWithPropertiesById($p["itemId"]);
            
            foreach($itemWithProperties["itemProperties"] as $properties) {
            
                $propertyId = $properties["propertyId"];
                $properyValue  = $properties["valueTexts"][0]["value"];
                if($properyValue == null) {
                    if($properties["valueInt"] != null) 
                        $properyValue = $properties["valueInt"];
                        
                    if($properyValue == null && $properties["valueFloat"] != null)
                        $properyValue = $properties["valueFloat"];                           
                }

                $propertyElement = [$propertyId => $properyValue];
                $arrayForMerge = $tempArr == null ? $p : $tempArr;
                $merge = array_merge($arrayForMerge,$propertyElement);  
                $tempArr = $merge;              
                $itemsResults[$i] = $merge;
            }
            
            $manufacturerId = $p["item"]["manufacturerId"];
            $originalManufacturer = $this->getManufacturerById($manufacturerId);

            if($originalManufacturer != null) {
                $manufacturerElement = [ "manufacturer-".$originalManufacturer["name"] => $originalManufacturer["name"]];
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

    public function getManufacturerById($id) 
    {
        $manufacturerRepo = pluginApp(ManufacturerRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $manufactures = null;

       $manufactures = $manufacturerRepo->findById($id);

        return $manufactures;
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

        // $repository = pluginApp(VariationRepositoryContract::class);       

        // $authHelper = pluginApp(AuthHelper::class);

        // $variation = null;

        // $variation = $authHelper->processUnguarded(
        //     function () use ($repository, $variation,$id) {
            
        //         return $repository->findById($id);
        //     }
        // );

        // return $variation;        
    
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
}

?>