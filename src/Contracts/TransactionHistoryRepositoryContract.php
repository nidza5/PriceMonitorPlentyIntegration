<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\TransactionHistory;
 
/**
 * Class TransactionHistoryRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface TransactionHistoryRepositoryContract
{
    /**
     * Save
     *
     * @param array $data
     * @return void
     */
    public function saveTransactionHistory(array $data);

    public function getAllTransactionHistory();

    public function getTransactionHistoryMasterCount($contractId,$type);

}