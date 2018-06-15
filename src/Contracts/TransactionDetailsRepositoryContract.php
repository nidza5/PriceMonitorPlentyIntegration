<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\TransactionDetails;
 
/**
 * Class TransactionDetailsRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface TransactionDetailsRepositoryContract
{
    /**
     * Save
     *
     * @param array $data
     * @return void
     */
    public function saveTransactionDetails(array $data);

}