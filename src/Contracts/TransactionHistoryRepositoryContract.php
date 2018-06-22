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
    public function saveTransactionHistoryMaster(array $data);

    public function getAllTransactionHistory();

    public function getTransactionHistoryMasterCount($contractId,$type);

    public function getTransactionById($id);

    public function getTransactionHistoryMaster($id,$priceMonitorContractId,$type);

    public function getTransactionHistoryMasterByCriteria( $contractId,$type, $transactionHistoryMasterId = null, $uniqueIdentifier = null);

    public function updateTransactionHistoryMasterState($transactionHistoryMaster,$transactionHistoryDetailsForSaving,$type, $transactionUniqueIdentifier,$allTransactionsDetailsInProgress);
}