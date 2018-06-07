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

    private $productRepo;

    public function __construct($productFilter,$productRepo)
    {
        $this->productFilter = $productFilter;
        $this->productRepo = $productRepo;
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
     public function saveFilter($contractIds, $type, $filter)
     {
        $f = $this->productRepo->getFilterByContractIdAndType($contractIds,"export_products");
       
         return $f;
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