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

    public function __construct($productRepo)
    {
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
     public function getFilter($idContract, $typeFilter)
     {
        //  $filterOriginal = $this->productRepo->getFilterByContractIdAndType($idContract,$typeFilter);

        //  return $filterOriginal;
         //  return ($filterOriginal !== null) ? $filterOriginal['serializedFilter'] : null;
     }
}

?>