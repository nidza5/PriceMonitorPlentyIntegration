<?php

use Patagona\Pricemonitor\Core\Interfaces\MapperService;

class MapperServices implements MapperService
{
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

    private $attributesMapping;
    private $contract;

    public function __construct($attributesMappings,$contract)
    {
        $this->attributesMapping = $attributesMappings;
        $this->contract = $contract;
    }

    /**
     * Gets mapping attributes
     *
     * @param $pricemonitorContractId
     *
     * @return array
     */
    public function getMappedAttributes($pricemonitorContractId)
    {
        $mappings = $this->attributesMapping;
        return $this->reformatMappings($mappings);
    }

    /**
     * Checks if all mandatory attributes are mapped.
     *
     * @param string $pricemonitorContractId
     *
     * @return bool
     */
    public function hasMandatoryMappings($pricemonitorContractId)
    {
        $mappings = $this->getMappedAttributes($pricemonitorContractId);
        $diff = array_diff(self::$mandatoryAttributes, array_column($mappings, 'pricemonitorCode'));
        return empty($diff);
    }

    /**
     * 
     *
     * @param string $pricemonitorContractId
     *
     * @return array
     */
    public function getMappedAttributeCodes($pricemonitorContractId)
    {
        $mappings = $this->attributesMapping->toArray();
        $mappingCodes = array_unique(array_column($mappings, 'attributeCode'));
        $mappingCodes[] = 'tax_class_id';
        return $mappingCodes;
    }

    public function convertToPricemonitor($contractId, $shopProduct)
    {
        $result = array('productId' => $shopProduct['entity_id']);
        $mappings = $this->attributesMapping;
        $contract = $this->contract;

        if (empty($mappings)) {
            return array();
        }

        $productAttributes = $this->_attributeMapperService->getProductAttributesCodeTypeHashMap($store->getId());

        /** @var Patagona_Pricemonitor_Model_AttributeMapping $mapping */
        foreach ($mappings as $mapping) {
            $attributeCode = $mapping->getAttributeCode();
            $pricemonitorCode = $mapping->getPricemonitorCode();

            if (!array_key_exists($attributeCode, $productAttributes)) {
                throw new \Exception('Attribute %s is not found in the shop.', $attributeCode);
            }

            $value = $shopProduct[$attributeCode];
            if (in_array($pricemonitorCode, self::$mandatoryAttributes)) {
                if ($pricemonitorCode === 'minPriceBoundary' || $pricemonitorCode === 'maxPriceBoundary') {
                    $value = $this->_priceCalculator->getCalculatedPrice(
                        $value,
                        $mapping->getValue(),
                        $mapping->getOperand()
                    );
                }

                if ($pricemonitorCode === 'referencePrice') {
                    /**
                     * Product object must be mocked, because core tax calculation requires an object not an array
                     * @var $product Mage_Catalog_Model_Product
                     */
                    $product = Mage::getModel('catalog/product')->addData($shopProduct);
                    $value = $this->_priceCalculator->getExportPrice($value, $product, $store);
                }

                $result[$pricemonitorCode] = $value;
            } else {
                $result['tags'][] = array(
                    'key' => $pricemonitorCode,
                    'value' => (string)$value
                );
            }
        }

        return $result;
    }

    /**
     * @param array $mappings
     *
     * @return array
     */
    protected function reformatMappings($mappings)
    {
        $result = array();

        foreach ($mappings as $mapping) {
            $result[] = array(
                'id' => $mapping['id'],
                'value' => $mapping['value'],
                'operand' => $mapping['operand'],
                'attributeCode' => $mapping['attributeCode'],
                'pricemonitorCode' => $mapping['priceMonitorCode'],
            );
        }

        return $result;
    }

    /**
     * @param string $contractId Pricemonitor contract id
     *
     * @return Patagona_Pricemonitor_Model_Resource_AttributeMapping_Collection
     */
    protected function getAttributesMapping($contractId)
    {
        /** @var Patagona_Pricemonitor_Model_AttributeMapping $attributeMappingModel */
        $attributeMappingModel = Mage::getModel('pricemonitor/attributeMapping');
        return $attributeMappingModel->getAttributesMappingByPricemonitorId($contractId);
    }

    /**
     * @param string $contractId Pricemonitor contract id
     *
     * @return null|Patagona_Pricemonitor_Model_Contract
     */
    protected function getContractById($contractId)
    {
        
        $contractModel = Mage::getModel('pricemonitor/contract');
        return $contractModel->getByPricemonitorId($contractId);
    }

}