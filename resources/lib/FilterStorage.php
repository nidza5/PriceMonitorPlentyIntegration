<?php

use Patagona\Pricemonitor\Core\Interfaces\FilterStorage as FilterStorageInterface;
use Patagona\Pricemonitor\Core\Infrastructure\Logger;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;

class FilterStorage implements FilterStorageInterface
{
    /**
    *
    * @var ProductFilterRepository
    */
    private $productFilter;

    public function __construct($productFilter)
    {
        $this->productFilter = $productFilter;
    }

    /**
     * Saves serialized filter.
     *
     * @param string $contractId Pricemonitor contract ID.
     * @param string $type Possible values export_products and import_prices.
     * @param string $filter Serialized Filter object.
     *
     * @return void
     */
     public function saveFilter($contractId, $type, $filter)
     {
        $filterArr = $this->productFilter->getFilterByContractIdAndType($contractId,"export_products");

         return $filterArr;
     }
 
     /**
      * Gets serialized filter from the DB.
      *
      * @param string $contractId Pricemonitor contract ID.
      * @param string $type Possible values export_products and import_prices.
      *
      * @return string|null
      */
     public function getFilter($contractId, $type)
     {
         return ($this->productFilter !== null) ? $this->productFilter['serializedFilter'] : null;
     }
}

?>