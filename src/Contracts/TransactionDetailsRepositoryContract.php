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

    public function getAllTransactionDetails();

    public function getTransactionHistoryDetailsCount($masterId);

    public function getTransactionHistoryDetailsByFilters($contractId,$transactionId,$uniqueIdentifier,$status);

    public function updateTransactionHistoryDetailsState( $transactionDetails, $type,$transactionUniqueIdentifier,$failedItems);
    
}