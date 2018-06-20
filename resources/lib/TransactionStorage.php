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

    public function __construct($transactionHistoryDetailsRecord = null,$totalDetailedRecords = 0,$transactionHistoryRecords = null,$totalHistoryRecords = 0)
    {
        $this->transactionHistoryDetailsRecord = $transactionHistoryDetailsRecord;
        $this->totalDetailedRecords = $totalDetailedRecords;
        $this->transactionHistoryRecords = $transactionHistoryRecords;
        $this->totalHistoryRecords = $totalHistoryRecords;
    }


    public function getTransactionHistoryMaster(TransactionHistoryMasterFilter $filter)
    {
       $transactionModels = $this->transactionHistoryRecords;

       $id = $filter->getId();

       if ($id !== null) {
            $transactionModels = array_filter($transactionModels, function ($u) use ($id) {
                return $u['id'] == $id;
            });
       }

       $uniqueIdentifier = $filter->getUniqueIdentifier();

       if ($uniqueIdentifier !== null) {
            $transactionModels = array_filter($transactionModels, function ($u) use ($uniqueIdentifier) {
                return $u['uniqueIdentifier'] == $uniqueIdentifier;
            });
       }

       $contractId = $filter->getContractId();

       if ($contractId !== null) {
            $transactionModels = array_filter($transactionModels, function ($u) use ($contractId) {
                return $u['priceMonitorContractId'] == $contractId;
            });
       }

       $type = $filter->getType();

       if ($type !== null) {
            $transactionModels = array_filter($transactionModels, function ($u) use ($type) {
                return $u['type'] == $type;
            });
       }

       //TO DO  SORT AND PAGINATE RECORD

    //    $transactionModels = $this->sortRecords($filter, $transactionModels);
    //    $transactionModels = $this->getPaginatedRecords($filter, $transactionModels);

       return $this->createTransactions($transactionModels);

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
        return $this->totalHistoryRecords;
    }

    public function getTransactionHistoryDetails(TransactionHistoryDetailFilter $filter)
    {
        $transactionDetailsModels = $this->transactionHistoryDetailsRecord;

        $id = $filter->getId();

        if($id !== null) {
            $transactionDetailsModels = array_filter($transactionDetailsModels, function ($u) use ($id) {
                return $u['id'] == $id;
            });
        }
        
        $transactionId = $filter->getMasterId();

        if ($transactionId !== null) {
            $transactionDetailsModels = array_filter($transactionDetailsModels, function ($u) use ($transactionId) {
                return $u['transactionId'] == $transactionId;
            });
        }

        $uniqueIdentifier = $filter->getMasterUniqueIdentifier();
        
        if ($uniqueIdentifier !== null) {
            $transactionDetailsModels = array_filter($transactionDetailsModels, function ($u) use ($uniqueIdentifier) {
                return $u['transactionUniqueIdentifier'] == $uniqueIdentifier;
            });
        }

        $status = $filter->getStatus();

        if ($status !== null) {
            $transactionDetailsModels = array_filter($transactionDetailsModels, function ($u) use ($status) {
                return $u['status'] == $status;
            });
        }

        //TO DO ORDER AND PAGINATED
        // $transactionDetailModels = $this->sortRecords($filter, $transactionDetailModels);
        // $transactionDetailModels = $this->getPaginatedRecords($filter, $transactionDetailModels);

        return $this->createTransactionDetails($transactionDetailModels);

    }

    public function getTransactionHistoryDetailsCount($masterId)
    {
       return $this->totalDetailedRecords;
    }

  
    public function saveTransactionHistory(TransactionHistoryMaster $transactionMaster, $transactionDetails = array())
    {
       throw new \Exception("SAve transaction history");
    }


    public function cleanupMaster($numberOfDays)
    {
      
    }


    public function cleanupDetails($numberOfDays)
    {
        
    }


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
                $transaction['priceMonitorContractId'],
                new DateTime($transaction['time']),
                $transaction['type'],
                $transaction['status'],
                $transaction['id'],
                $transaction['uniqueIdentifier']
            );

            $createdMasterTransaction->setFailedCount((int)$transaction['failedCount']);
            $createdMasterTransaction->setTotalCount((int)$transaction['totalCount']);
            $createdMasterTransaction->setNote($transaction['note']);
            $createdMasterTransaction->setSuccessCount((int)$transaction['successCount']);

            $createdMasterTransactions[] = $createdMasterTransaction;
        }

        return $createdMasterTransactions;
    }


    protected function createTransactionDetails($transactionDetailModels)
    {
        $createdTransactionsDetails = array();

        foreach ($transactionDetailModels as $transactionDetail) {
            $createdTransaction = new TransactionHistoryDetail(
                $transactionDetail['status'],
                new DateTime($transactionDetail['time']),
                $transactionDetail['id'],
                $transactionDetail['transactionId'],
                $transactionDetail['transactionUniqueIdentifier'],
                $transactionDetail['productId'],
                $transactionDetail['gtin'],
                $transactionDetail['productName'],
                (float)$transactionDetail['referencePrice'],
                (float)$transactionDetail['minPrice'],
                (float)$transactionDetail['maxPrice']
            );

            $createdTransaction->setNote($transactionDetail['note']);
            $createdTransaction->setUpdatedInShop((bool)$transactionDetail['isUpdated']);

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