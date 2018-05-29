<?php

use Patagona\Pricemonitor\Core\Interfaces\FilterStorage;
use Patagona\Pricemonitor\Core\Infrastructure\Logger;

class FilterService implements FilterStorage
{

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
         /** @var Patagona_Pricemonitor_Model_ProductFilter $productFilter */
        //  $productFilter = $this->getFilterModel($contractId, $type);
 
        //  $productFilter->setPricemonitorContractId($contractId);
        //  $productFilter->setType($type);
        //  $productFilter->setSerializedFilter($filter);
 
        //  try {
        //      $productFilter->save();
        //  } catch (Exception $e) {
        //      Logger::logError($e->getMessage());
        //      Mage::throwException($e->getMessage());
        //  }
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
         /** @var Patagona_Pricemonitor_Model_ProductFilter $filterModel */
        //  $filterModel = $this->getFilterModel($contractId, $type);
 
        //  return $filterModel->getSerializedFilter();
     }
 
     /**
      * Get filter by Pricemonitor contract ID and type.
      *
      * @param string $contractId
      * @param string $type
      *
      * @return Patagona_Pricemonitor_Model_ProductFilter
      */
     protected function getFilterModel($contractId, $type)
     {
        //  /** @var Patagona_Pricemonitor_Model_Resource_ProductFilter_Collection $productFilterCollection */
        //  $productFilterCollection = Mage::getModel('pricemonitor/productFilter')->getCollection();
        //  $productFilterCollection->addFieldToFilter('pricemonitor_contract_id', $contractId);
        //  $productFilterCollection->addFieldToFilter('type', $type);
        //  /** @var Patagona_Pricemonitor_Model_ProductFilter $productFilter */
        //  $productFilter = $productFilterCollection->setPageSize(1)->getLastItem();
        //  return $productFilter;
     }
}

?>