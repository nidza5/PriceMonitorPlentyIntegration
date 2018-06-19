<?php

require_once __DIR__ . '/MapperService.php';

use Patagona\Pricemonitor\Core\Interfaces\ProductService;
use Patagona\Pricemonitor\Core\Sync\Filter\Filter;

class ProductServices implements ProductService
{
    
   private $contract;
   private $mapperService;
   private $productForExport;

    public function __construct($contract,$mapperService,$productForExport)
    {
        $this->contract = $contract;
        $this->mapperService = $mapperService;
        $this->productForExport = $productForExport;
    }

    public function exportProducts($contractId, $filter, array $shopCodes = array())
    {
        return $this->productForExport;
    }

    /**
     * Gets product identifier field name, that will be used for querying integration storage.
     *
     * @return string
     */
    public function getProductIdentifier()
    {
        return 'entity_id';
    }
}