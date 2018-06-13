<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
use PriceMonitorPlentyIntegration\Models\AttributeMapping;
 
class AttributesMappingRepository implements AttributesMappingRepositoryContract
{
     /**
     * saveAttributeMapping
     * @param array $data 
     */
     public function  saveAttributeMapping($contractId, $contractPricemonitorId,array $mappings)
     {
        
          $this->deleteMappingsForContract($contractPricemonitorId);

          $database = pluginApp(DataBase::class);

          foreach($mappings as $mapping) {
               $attributeMapping = pluginApp(AttributeMapping::class);
               $attributeMapping->attributeCode = $mapping['attributeCode'];
               $attributeMapping->priceMonitorCode = $mapping['pricemonitorCode'];
               $attributeMapping->operand = $mapping['operand'];
               $attributeMapping->value = $mapping['value'];
               $attributeMapping->contractId = $contractId;
               $attributeMapping->priceMonitorContractId = $contractPricemonitorId;
               $database->save($attributeMapping);
          }
         
     }

     public function getAttributeMappingByPriceMonitorId($priceMonitorId): AttributeMapping
     {
        $databaseAttributeMapping = pluginApp(DataBase::class);
        $attributeMappingOriginal = $databaseAttributeMapping->query(AttributeMapping::class)->where('priceMonitorContractId', '=', $priceMonitorId)->get();

        if($attributeMappingOriginal == null)
          return pluginApp(AttributeMapping::class);

        return $attributeMappingOriginal[0];
     }

    public function getAttributeMappingCollectionByPriceMonitorId($priceMonitorId)
    {
        $databaseAttributeMapping = pluginApp(DataBase::class);
        $attributeMappingOriginal = $databaseAttributeMapping->query(AttributeMapping::class)->where('priceMonitorContractId', '=', $priceMonitorId)->get();

        return $attributeMappingOriginal;
    }

     public function deleteMappingsForContract($contractPriceMonitorId) 
     {
        $database = pluginApp(DataBase::class);
        $mappingforDelete = $this->getAttributeMappingCollectionByPriceMonitorId($contractPriceMonitorId);
        
        foreach($mappingforDelete as $mapDelete)
        {
            $database->delete($mapDelete);
        }
     }

     public function getAllAttributeMappings() 
     {
        $database = pluginApp(DataBase::class);
        $attMappingList = $database->query(AttributeMapping::class)->get();
        
        return $attMappingList;

     }
}