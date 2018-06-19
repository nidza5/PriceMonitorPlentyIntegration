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
     * Constructor.
     *
     * @param AttributeValueRepositoryContract $attributeValueRepo
     * @param PropertyRepositoryContract $propRepo
     * @param AttributeRepositoryContract $attributeRepo
     */
    public function __construct(PropertyRepositoryContract $propertyRepository,AttributeRepositoryContract $attributeRepository,AttributeValueRepositoryContract $attributeValueRepository,ItemDataLayerRepositoryContract $itemDataLayerRepo )
    {
        $this->propertyRepository = $propertyRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeValueRepository = $attributeValueRepository;
        $this->itemDataLayerRepo = $itemDataLayerRepo;
    }

    public function getProducts()
    {
        // if(hasMandatoryMappings($mappedAttributes)) {
        //     throw new \Exception("Mandatory fields must be mapped!");
        // }

        $itemColumns = [
            'itemDescription' => [
                'name1',
                'description'
            ],
            'variationBase' => [
                'id'
            ],
            'variationRetailPrice' => [
                'price'
            ],
            'itemPropertyList' => []
        ];
 
        $itemFilter = [
            'itemBase.isStoreSpecial' => [
                'shopAction' => [3]
            ]
        ];
 
        $itemParams = [
            'language' => 'en'
        ];
 
        $resultItems = $this->itemDataLayerRepo
            ->search($itemColumns, $itemFilter, $itemParams);
 
        $items = array();
        foreach ($resultItems as $item)
        {
            $items[] = $item;
        }

          return $items;
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