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
     * @return ProductFilter
     */
    public function getFilterByContractId($contractId) : ProductFilter;

    /**
     * Save product filter
     *
     * @param array $productFilterData
     * @return void
     */

    public function saveProductFilter(array $data);

 
    

}