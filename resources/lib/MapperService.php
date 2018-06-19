<?php

use Patagona\Pricemonitor\Core\Interfaces\MapperService;

class MapperServices implements MapperService
{
    // /**
    //  * Mandatory attributes
    //  *
    //  * @var array
    //  */
    // public static $mandatoryAttributes = array(
    //     'gtin',
    //     'name',
    //     'referencePrice',
    //     'minPriceBoundary',
    //     'maxPriceBoundary'
    // );

    // /**
    //  * @var Patagona_Pricemonitor_Helper_Data
    //  */
    // protected $_baseHelper;
    // /**
    //  * @var Patagona_Pricemonitor_Helper_PriceCalculator
    //  */
    // protected $_priceCalculator;
    // /**
    //  * @var Patagona_Pricemonitor_Service_AttributeService
    //  */
    // protected $_attributeMapperService;

    // /**
    //  * Patagona_Pricemonitor_Service_Core_MapperService constructor.
    //  */
    // public function __construct()
    // {
    //     $this->_baseHelper = Mage::helper('pricemonitor');
    //     $this->_priceCalculator = Mage::helper('pricemonitor/priceCalculator');
    //     $this->_attributeMapperService = new Patagona_Pricemonitor_Service_AttributeService();
    // }

    // /**
    //  * Gets mapping attributes
    //  *
    //  * @param $pricemonitorContractId
    //  *
    //  * @return array
    //  */
    // public function getMappedAttributes($pricemonitorContractId)
    // {
    //     $mappings = $this->getAttributesMapping($pricemonitorContractId)->toArray();
    //     return $this->reformatMappings($mappings['items']);
    // }

    // /**
    //  * Checks if all mandatory attributes are mapped.
    //  *
    //  * @param string $pricemonitorContractId
    //  *
    //  * @return bool
    //  */
    // public function hasMandatoryMappings($pricemonitorContractId)
    // {
    //     $mappings = $this->getMappedAttributes($pricemonitorContractId);
    //     $diff = array_diff(self::$mandatoryAttributes, array_column($mappings, 'pricemonitorCode'));
    //     return empty($diff);
    // }

    // /**
    //  * Gets Magneto mapped attribute codes.
    //  *
    //  * @param string $pricemonitorContractId
    //  *
    //  * @return array
    //  */
    // public function getMappedAttributeCodes($pricemonitorContractId)
    // {
    //     $mappings = $this->getAttributesMapping($pricemonitorContractId)->toArray();
    //     $mappingCodes = array_unique(array_column($mappings['items'], 'attribute_code'));
    //     $mappingCodes[] = 'tax_class_id';
    //     return $mappingCodes;
    // }

    // /**
    //  * Converts integration specific products to Pricemonitor product format
    //  *
    //  * @param string $contractId Pricemonitor contract id
    //  * @param array $shopProduct Shop products that needs to be converted to Pricemonitor product format
    //  *
    //  * @return array Converted Pricemonitor product
    //  * @throws Mage_Core_Exception
    //  */
    // public function convertToPricemonitor($contractId, $shopProduct)
    // {
    //     $result = array('productId' => $shopProduct['entity_id']);
    //     $mappings = $this->getAttributesMapping($contractId);
    //     $contract = $this->getContractById($contractId);

    //     if (empty($mappings)) {
    //         return array();
    //     }

    //     $store = $this->_baseHelper->getStore($contract->getStoreView());
    //     $productAttributes = $this->_attributeMapperService->getProductAttributesCodeTypeHashMap($store->getId());

    //     /** @var Patagona_Pricemonitor_Model_AttributeMapping $mapping */
    //     foreach ($mappings as $mapping) {
    //         $attributeCode = $mapping->getAttributeCode();
    //         $pricemonitorCode = $mapping->getPricemonitorCode();

    //         if (!array_key_exists($attributeCode, $productAttributes)) {
    //             Mage::throwException($this->_baseHelper->__('Attribute %s is not found in the shop.', $attributeCode));
    //         }

    //         $value = $shopProduct[$attributeCode];
    //         if (in_array($pricemonitorCode, self::$mandatoryAttributes)) {
    //             if ($pricemonitorCode === 'minPriceBoundary' || $pricemonitorCode === 'maxPriceBoundary') {
    //                 $value = $this->_priceCalculator->getCalculatedPrice(
    //                     $value,
    //                     $mapping->getValue(),
    //                     $mapping->getOperand()
    //                 );
    //             }

    //             if ($pricemonitorCode === 'referencePrice') {
    //                 /**
    //                  * Product object must be mocked, because core tax calculation requires an object not an array
    //                  * @var $product Mage_Catalog_Model_Product
    //                  */
    //                 $product = Mage::getModel('catalog/product')->addData($shopProduct);
    //                 $value = $this->_priceCalculator->getExportPrice($value, $product, $store);
    //             }

    //             $result[$pricemonitorCode] = $value;
    //         } else {
    //             $result['tags'][] = array(
    //                 'key' => $pricemonitorCode,
    //                 'value' => (string)$value
    //             );
    //         }
    //     }

    //     return $result;
    // }

    // /**
    //  * @param array $mappings
    //  *
    //  * @return array
    //  */
    // protected function reformatMappings($mappings)
    // {
    //     $result = array();

    //     foreach ($mappings as $mapping) {
    //         $result[] = array(
    //             'id' => $mapping['id'],
    //             'value' => $mapping['value'],
    //             'operand' => $mapping['operand'],
    //             'attributeCode' => $mapping['attribute_code'],
    //             'pricemonitorCode' => $mapping['pricemonitor_code'],
    //         );
    //     }

    //     return $result;
    // }

    // /**
    //  * @param string $contractId Pricemonitor contract id
    //  *
    //  * @return Patagona_Pricemonitor_Model_Resource_AttributeMapping_Collection
    //  */
    // protected function getAttributesMapping($contractId)
    // {
    //     /** @var Patagona_Pricemonitor_Model_AttributeMapping $attributeMappingModel */
    //     $attributeMappingModel = Mage::getModel('pricemonitor/attributeMapping');
    //     return $attributeMappingModel->getAttributesMappingByPricemonitorId($contractId);
    // }

    // /**
    //  * @param string $contractId Pricemonitor contract id
    //  *
    //  * @return null|Patagona_Pricemonitor_Model_Contract
    //  */
    // protected function getContractById($contractId)
    // {
    //     /** @var Patagona_Pricemonitor_Model_Contract $contractModel */
    //     $contractModel = Mage::getModel('pricemonitor/contract');
    //     return $contractModel->getByPricemonitorId($contractId);
    // }

}