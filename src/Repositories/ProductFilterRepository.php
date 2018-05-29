<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Models\ProductFilter;
 
class ProductFilterRepository implements ProductFilterRepositoryContract
{
     /**
     * Save productFilter
     * @param array $data 
     */
     public function saveProductFilter(array $data)
     {
         
     }

      /**
     * Get filter by contractId 
     *
     * @return ProductFilter
     */
     public function getFilterByContractIdAndType($contractId,$type) 
     {
        if($contractId == 0 || $contractId == null)
            return null;

        $database = pluginApp(DataBase::class);
        $productFilter = $database->query(ProductFilter::class)->where('contractId', '=', $contractId)->where('type', '=', $type)->get();
        return $productFilter[0];

     }
}