<?php

use Patagona\Pricemonitor\Core\Interfaces\ProductService as ProductServiceInterface;
// use Patagona\Pricemonitor\Core\Sync\Filter\Filter;

class ProductService implements ProductServiceInterface
{
    
   private $contract;
   private $productForExport;

    public function __construct($contract,$productForExport)
    {
        $this->contract = $contract;
        $this->productForExport = $productForExport;
    }

    public function exportProducts($contractId, $filter, array $shopCodes = array())
    {
        return $this->productForExport;
    }

    public function getProductIdentifier()
    {
        return 'id';
    }
}