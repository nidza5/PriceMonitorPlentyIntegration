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
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);
 
        $productFilter = pluginApp(ProductFilter::class);

        $contractId = $data['contractId'];

        $filterType = $data['filterType'];

        $serializedFilter = (string)$data['filter'];
 
        $productFilter->contractId = $contractId;

        $productFilter->type = $filterType;

        $productFilter->serializedFilter = $serializedFilter;
 
        $database->save($productFilter);
 
     }

     public function getAllFilters() : array
     {
        $database = pluginApp(DataBase::class);
        $productFilterList = $database->query(ProductFilter::class)->get();
        
        return $productFilterList;
     }

      /**
     * Get filter by contractId 
     *
     * @return ProductFilter
     */
     public function getFilterByContractIdAndType($contractId,$type) : ProductFilter
     {
        if($contractId == 0 || $contractId == null)
            return pluginApp(ProductFilter::class);

        $database = pluginApp(DataBase::class);
        $productFilter = $database->query(ProductFilter::class)->where('contractId', '=', $contractId)->where('type', '=', $type)->get();
        return $productFilter[0] === null ? pluginApp(ProductFilter::class) : $productFilter[0];

     }

    public function deleteAllProductFilter()
    {
        $database = pluginApp(DataBase::class);
 
        $productFilterList = $database->query(ProductFilter::class)->get();
 
        foreach($productFilterList as $con)
        {
            $database->delete($con);
        }
    }

}