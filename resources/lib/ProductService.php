<?php

use Patagona\Pricemonitor\Core\Interfaces\ProductService as ProductServiceInterface;
use Patagona\Pricemonitor\Core\Sync\Filter\Filter;

class ProductService implements ProductServiceInterface
{
    
   private $contract;
   private $productForExport;

    public function __construct($contract,$productForExport)
    {
        $this->contract = $contract;
        $this->productForExport = $productForExport;
    }
    
    public function exportProducts($contractId, Filter $filter, array $shopCodes = array())
    {
        return $this->productForExport;
    }

    public function getProductIdentifier()
    {
        return 'id';
    }

    protected function isValidContract($contract)
    {
        return !empty($this->contract);
    }

    protected function isValidFilter($filter)
    {
        if (empty($filter)) {
            return false;
        }

        $hasExpression = false;
        foreach ($filter->getExpressions() as $group) {
            $expressions = $group->getExpressions();
            if (!empty($expressions)) {
                $hasExpression = true;
            }
        }

        return $hasExpression;
    }

    protected function getContractById($contractId)
    {
        return $this->contract;
        
    }
}