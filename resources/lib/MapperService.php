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

    public static $operands = array(
        null,
        'add',
        'sub',
        'mul',
        'div',
    );

    private $attributesMapping;
    private $contract;
    private $productsForExport;
    private $productAttributes;

    public function __construct($attributesMappings = null,$contract = null,$productsForExport,$productAttributes = null)
    {
        $this->attributesMapping = $attributesMappings;
        $this->contract = $contract;
        $this->productsForExport = $productsForExport;
        $this->productAttributes = $productAttributes;
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

    public function getCalculatedPrice($shopValue, $calculationValue, $operand)
    {
        $result = (double)$shopValue;
        $value = (double)$calculationValue;

        if (!in_array($operand, self::$operands)) {
            throw new \Exception('Operand %s is not supported.', $operand);
        }

        switch ($operand) {
            case 'add':
                $result = $shopValue + $value;
                break;
            case 'sub':
                $result = $shopValue - $value;
                break;
            case 'mul':
                $result = $shopValue * $value;
                break;
            case 'div':
                $result = $shopValue / $value;
                break;
        }

        return $result;
    }

    public function convertToPricemonitor($contractId, $shopProduct)
    {
        
        foreach($shopProduct as $product)
        {   
            $result = array('productId' => $product['id']);
            $mappings = $this->attributesMapping;
            $contract = $this->contract;
    
            if (empty($mappings)) {
                return array();
            }
    
            //Attribute from plenty markets
            $productAttributes = $this->productAttributes;
    
            foreach($mappings as $mapping) {
                $attributeCode = $mapping['attributeCode'];
                $priceMonitorCode = $mapping['priceMonitorCode'];
                
                $columnNameInShopProduct = $productAttributes[$attributeCode];
    
                $value = $product[$columnNameInShopProduct];
    
                if (in_array($pricemonitorCode, self::$mandatoryAttributes)) {
                    if ($pricemonitorCode === 'minPriceBoundary' || $pricemonitorCode === 'maxPriceBoundary') {
                        $value = $this->getCalculatedPrice(
                            $value,
                            $mapping['value'],
                            $mapping['operand']
                        );
                    }
    
                    $result[$pricemonitorCode] = $value;
                } else {
                    $result['tags'][] = array(
                        'key' => $pricemonitorCode,
                        'value' => (string)$value
                    );
                }        
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