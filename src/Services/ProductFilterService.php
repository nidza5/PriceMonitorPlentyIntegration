<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;

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

    public function getProducts($filter,$mappedAttributes)
    {
        if(hasMandatoryMappings($mappedAttributes)) {
            throw new \Exception("Mandatory fields must be mapped!");
        }
    
        $mappedAttributesCodes = $this->hasMandatoryMappings($mappedAttributes);

        if($mappedAttributesCodes == null ||  empty($mappedAttributesCodes) )
            throw new \Exception("Mapped attributes codes doesn't exist!");

            $productsRepo = pluginApp(ItemRepositoryContract::class);

            $authHelperAttr = pluginApp(AuthHelper::class);
            
            $productsOriginal = null;
    
            $productsOriginal = $authHelperAttr->processUnguarded(
              function () use ($productsRepo, $productsOriginal) {
              
                  return $productsRepo->all();
              }
          );
    
           $resultItems = $productsOriginal->toArray();

          return $resultItems;
    }

    public function hasMandatoryMappings($mappings)
    {
        $diff = array_diff(self::$mandatoryAttributes, array_column($mappings, 'priceMonitorCode'));
        return empty($diff);
    }

    public function getMappedAttributesCode($mappedAttribute) {
        $mappings = $mappedAttribute->toArray();
        $mappingCodes = array_unique(array_column($mappings, 'attribute_code'));
        $mappingCodes[] = 'tax_class_id';
        return $mappingCodes;
    }

}

?>