<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
use PriceMonitorPlentyIntegration\Models\AttributeMapping;
 
class AttributeMappingRepository implements AttributesMappingRepositoryContract
{
     /**
     * Save productFilter
     * @param array $data 
     */
     public function saveAttributeMapping(array $data)
     {
        /**
         * @var DataBase $database
         */

         if($data == null)
            return; 
     }

     public function getAttributeMappingByPriceMonitorId($priceMonitorId): AttributeMapping
     {
        $databaseAttributeMapping = pluginApp(DataBase::class);
        $attributeMappingOriginal = $databaseAttributeMapping->query(AttributeMapping::class)->where('priceMonitorContractId', '=', $priceMonitorId)->get();

        if($attributeMappingOriginal == null)
          return pluginApp(AttributeMapping::class);

        return $attributeMappingOriginal[0];
     }
}