<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\ProductFilter;
 
/**
 * Class ProductFilterRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface ProductFilterRepositoryContract
{
    /**
     * Get product filter by contractId
     *
     * @param int $contractId
     * @param string $type
     * @return ProductFilter
     */
    public function getFilterByContractIdAndType($contractId,$type) : ProductFilter;

    public function getAllFilters() :array;

    /**
     * Save product filter
     *
     * @param array $productFilterData
     * @return void
     */

    public function saveProductFilter(array $data);

 
    

}