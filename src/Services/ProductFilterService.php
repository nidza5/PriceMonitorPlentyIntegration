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

           
            
        // $repository->setFilters([
        //         'barcode' => '555'
        //     ]);

            $repository->setSearchParams([
                'with' => [
                    'variationAttributeValues' => null,
                    'variationBarcodes' => 'barcode',
                    'item' => null,
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
                foreach($p['variationBarcodes'] as &$bar) {

                    $barCode = null;

                    $barCode = $authHelper->processUnguarded(
                        function () use ($barCodeRepo, $barCode,$bar) {
                        
                            return $barCodeRepo->findBarcodeById($bar['barcodeId']);
                        }
                    );
                    
                    $barElement = [$barCode->name => $bar['code']];

                    $merge = array_merge($p,$barElement);
                    $itemsResults[$i] = $merge;

                }
               
           }

           return $itemsResults;
          // return $products->getResult();
    }

    // public function hasMandatoryMappings($mappings)
    // {
    //     $diff = array_diff(self::$mandatoryAttributes, array_column($mappings, 'priceMonitorCode'));
    //     return empty($diff);
    // }

    public function getMappedAttributesCode($mappedAttribute) {
        $mappings = $mappedAttribute->toArray();
        $mappingCodes = array_unique(array_column($mappings, 'attribute_code'));
        $mappingCodes[] = 'tax_class_id';
        return $mappingCodes;
    }

}

?>