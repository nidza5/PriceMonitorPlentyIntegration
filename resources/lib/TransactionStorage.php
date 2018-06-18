<?php

use Patagona\Pricemonitor\Core\Infrastructure\Logger;
use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
use Patagona\Pricemonitor\Core\Interfaces\LoggerService;
use Patagona\Pricemonitor\Core\Interfaces\TransactionHistoryStorage;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryDetail;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryDetailFilter;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryMaster;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryMasterFilter;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistorySortFields;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryStorageDTO;

class TransactionStorage implements TransactionHistoryStorage
{
    private $transactionHistoryDetailsRecord;
    private $totalDetailedRecords;
    private $transactionHistoryRecords;
    private $totalHistoryRecords;

    public function __construct($transactionHistoryDetailsRecord,$totalDetailedRecords,$transactionHistoryRecords,$totalHistoryRecords)
    {
        $this->transactionHistoryDetailsRecord = $transactionHistoryDetailsRecord;
        $this->totalDetailedRecords = $totalDetailedRecords;
        $this->transactionHistoryRecords = $transactionHistoryRecords;
        $this->totalHistoryRecords = $totalHistoryRecords;
    }

    /**
     * Gets transaction master data based on passed filters.
     *
     * @param TransactionHistoryMasterFilter $filter
     *
     * @return TransactionHistoryMaster[]
     */
    public function getTransactionHistoryMaster(TransactionHistoryMasterFilter $filter)
    {
       
    }

    /**
     * Get number of transactions for specific contract and type.
     *
     * @param string $contractId
     * @param string $type
     *
     * @return int
     */
    public function getTransactionHistoryMasterCount($contractId, $type)
    {
       
    }

    /**
     * Gets transaction history details based on set filters.
     *
     * @param TransactionHistoryDetailFilter $filter
     *
     * @return \Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryDetail[]
     */
    public function getTransactionHistoryDetails(TransactionHistoryDetailFilter $filter)
    {
        
    }

    /**
     * Gets number of transaction details for master transaction.
     *
     * @param int $masterId
     *
     * @return int
     */
    public function getTransactionHistoryDetailsCount($masterId)
    {
      
    }

    /**
     * Saves transaction history master and details. If id is set for master or for details UPDATE should be done.
     * If id is null, INSERT of the new transaction should be done. Array od TransactionHistoryStorageDTO should be
     * returned which will contain affected transactions.
     *
     * @param TransactionHistoryMaster $transactionMaster
     * @param array $transactionDetails
     *
     * @return TransactionHistoryStorageDTO
     */
    public function saveTransactionHistory(TransactionHistoryMaster $transactionMaster, $transactionDetails = array())
    {
       
    }

    /**
     * Deletes transaction master records, when date of creation is older than it is set for cleanup period.
     *
     * @param int $numberOfDays
     *
     * @return bool
     */
    public function cleanupMaster($numberOfDays)
    {
      
    }

    /**
     * Deletes transaction details records, when date of creation is older than it is set for cleanup period.
     *
     * @param int $numberOfDays
     *
     * @return bool
     */
    public function cleanupDetails($numberOfDays)
    {
        
    }

    /**
     * @param TransactionHistoryMasterFilter|TransactionHistoryDetailFilter $filter
     * @param $records
     *
     * @return Patagona_Pricemonitor_Model_Resource_TransactionHistory_Collection
     */
    protected function sortRecords($filter, $records)
    {
        $orderBy = $filter->getOrderBy();

        if (!empty($orderBy)) {
            foreach (array_keys($orderBy) as $orderKey) {
                if ($orderKey === TransactionHistorySortFields::DATE_OF_CREATION) {
                    $records->setOrder('time', $orderBy[$orderKey]);
                }
            }
        }

        return $records;
    }

   
    protected function getPaginatedRecords($filter, $records)
    {
        $offset = $filter->getOffset();
        $limit = $filter->getLimit();

        if ($limit !== null) {
            $records->limitAndOffset($limit, $offset);
        }

        return $records;
    }

   
    protected function createTransactions($transactions)
    {
        $createdMasterTransactions = array();

        /** @var Patagona_Pricemonitor_Model_TransactionHistory $transaction */
        foreach ($transactions as $transaction) {
            $createdMasterTransaction = new TransactionHistoryMaster(
                $transaction->getPricemonitorContractId(),
                new DateTime($transaction->getTime()),
                $transaction->getType(),
                $transaction->getStatus(),
                $transaction->getId(),
                $transaction->getUniqueIdentifier()
            );

            $createdMasterTransaction->setFailedCount((int)$transaction->getFailedCount());
            $createdMasterTransaction->setTotalCount((int)$transaction->getTotalCount());
            $createdMasterTransaction->setNote($transaction->getNote());
            $createdMasterTransaction->setSuccessCount((int)$transaction->getSuccessCount());

            $createdMasterTransactions[] = $createdMasterTransaction;
        }

        return $createdMasterTransactions;
    }

    /**
     * @param Patagona_Pricemonitor_Model_TransactionHistory|object $transactionDetailModels
     *
     * @return TransactionHistoryDetail[]
     */
    protected function createTransactionDetails($transactionDetailModels)
    {
        $createdTransactionsDetails = array();

        /** @var Patagona_Pricemonitor_Model_TransactionHistoryDetail $transactionDetail */
        foreach ($transactionDetailModels as $transactionDetail) {
            $createdTransaction = new TransactionHistoryDetail(
                $transactionDetail->getStatus(),
                new DateTime($transactionDetail->getTime()),
                $transactionDetail->getId(),
                $transactionDetail->getTransactionId(),
                $transactionDetail->getTransactionUniqueIdentifier(),
                $transactionDetail->getProductId(),
                $transactionDetail->getGtin(),
                $transactionDetail->getProductName(),
                (float)$transactionDetail->getReferencePrice(),
                (float)$transactionDetail->getMinPrice(),
                (float)$transactionDetail->getMaxPrice()
            );

            $createdTransaction->setNote($transactionDetail->getNote());
            $createdTransaction->setUpdatedInShop((bool)$transactionDetail->getIsUpdated());

            $createdTransactionsDetails[] = $createdTransaction;
        }

        return $createdTransactionsDetails;
    }

    protected function saveMasterTransaction(TransactionHistoryMaster $transactionHistoryMaster)
    {
        
    }

    protected function saveTransactionHistoryDetails($transactionHistoryDetails)
    {
        
    }

    protected function saveTransactionDetail(TransactionHistoryDetail $transactionDetail)
    {
       
    }

    /**
     * @param int $numberOfDays
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $records
     *
     * @return bool
     */
    protected function cleanUp($numberOfDays, $records)
    {
       
    }

    protected function fillTransactionDetailModel(TransactionHistoryDetail $transactionDetail, $transactionDetailModel)
    {
        if ($transactionDetail->getStatus() !== null) {
            $transactionDetailModel->setStatus($transactionDetail->getStatus());
        }

        if ($transactionDetail->getTime() !== null) {
            $transactionDetailModel->setTime($transactionDetail->getTime()->format('Y-m-d H:i:s'));
        }

        $transactionDetailModel->setTransactionId($transactionDetail->getMasterId());
        $transactionDetailModel->setTransactionUniqueIdentifier($transactionDetail->getMasterUniqueIdentifier());
        $transactionDetailModel->setProductId($transactionDetail->getProductId());
        $transactionDetailModel->setGtin($transactionDetail->getGtin());
        $transactionDetailModel->setProductName($transactionDetail->getProductName());
        $transactionDetailModel->setReferencePrice((float)$transactionDetail->getReferencePrice());
        $transactionDetailModel->setMinPrice((float)$transactionDetail->getMinPrice());
        $transactionDetailModel->setMaxPrice((float)$transactionDetail->getMaxPrice());
        $transactionDetailModel->setNote($transactionDetail->getNote());
        $transactionDetailModel->setIsUpdated($transactionDetail->isUpdatedInShop());
    }

}