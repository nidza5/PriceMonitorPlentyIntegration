<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
use PriceMonitorPlentyIntegration\Models\TransactionHistory;
use PriceMonitorPlentyIntegration\Constants\FilterType;
use PriceMonitorPlentyIntegration\Constants\TransactionStatus;
 
class TransactionHistoryRepository implements TransactionHistoryRepositoryContract
{
     /**
     * Save
     *
     * @param array $data
     * @return void
     */
    public function saveTransactionHistoryMaster(array $data)
    {
        if($data == null)
            return;

        $database = pluginApp(DataBase::class);
        $transactionHistory = $this->getTransactionById($data['id']);
        
        if($data['uniqueIdentifier'] != null) {
            $transactionHistory->uniqueIdentifier = $data['uniqueIdentifier'];            
        }    
        
        if($data['time'] != null) {
            $transactionHistory->time = implode("-",$data['time']); 
        }

        $transactionHistory->status = $data['status']; 
        $transactionHistory->note = $data['note']; 
        $transactionHistory->totalCount = $data['totalCount']; 
        $transactionHistory->successCount = $data['successCount']; 
        $transactionHistory->failedCount = $data['failedCount']; 
        $transactionHistory->type = $data['type']; 
        $transactionHistory->priceMonitorContractId = $data['contractId']; 

        $database->save($transactionHistory);

        return $transactionHistory;
    }

    public function getTransactionHistoryMaster($id,$priceMonitorContractId,$type) 
    {
        $databaseTransactionHistory = pluginApp(DataBase::class);
        $transactionOriginalCollection = $databaseTransactionHistory->query(TransactionHistory::class)->where('id', '=', $id)->where('priceMonitorContractId', '=', $priceMonitorContractId)->where('type', '=', $type)->get();
        
        return $transactionOriginalCollection;
    }

    public function getTransactionById($id)
    {
        $databaseTransactionHistory = pluginApp(DataBase::class);
        $transactionOriginal = $databaseTransactionHistory->query(TransactionHistory::class)->where('id', '=', $id)->get();

        if($transactionOriginal == null)
            return pluginApp(TransactionHistory::class);

      return $transactionOriginal[0];
      
    }

    public function getAllTransactionHistory() 
    {
        $database = pluginApp(DataBase::class);
        $transactionHistory = $database->query(TransactionHistory::class)->get();
        
        return $transactionHistory;
    }

    public function getTransactionHistoryMasterCount($contractId,$type)
    {
        $database = pluginApp(DataBase::class);
        $transactionHistoryCount = $database->query(TransactionHistory::class)->where('priceMonitorContractId', '=', $contractId)->where('type', '=', $type)->count();
        
        return $transactionHistoryCount;

    }

    public function getTransactionHistoryMasterByCriteria($contractId,$type, $transactionHistoryMasterId = null, $uniqueIdentifier = null) {
        $database = pluginApp(DataBase::class);

        if($transactionHistoryMasterId == null) 
            $transactionHistoryMaster = $database->query(TransactionHistory::class)->where('priceMonitorContractId', '=', $contractId)->where('uniqueIdentifier', '=', $uniqueIdentifier)->where('type', '=', $type)->get();
        else 
            $transactionHistoryMaster = $database->query(TransactionHistory::class)->where('priceMonitorContractId', '=', $contractId)->where('uniqueIdentifier', '=', $uniqueIdentifier)->where('type', '=', $type)->where('id', '=', $transactionHistoryMasterId)->get();
        
        return $transactionHistoryMaster;

    }

    public function updateTransactionHistoryMasterState($transactionHistoryMaster,$transactionHistoryDetailsForSaving,$type, $transactionUniqueIdentifier,$allTransactionsDetailsInProgress)
    {
        $failedCountBeforeUpdate = $transactionHistoryMaster["failedCount"];
        $filteredOutCount = 0;

        $database = pluginApp(DataBase::class);

        foreach ($transactionHistoryDetailsForSaving as $transactionDetail) {

            $master = $this->getTransactionById($transactionDetail["id"]);

            if ($transactionDetail["id"] === null) {
                $master->totalCount = $transactionHistoryMaster["totalCount"]+ 1;
                $transactionAlreadyCounted = false;
            } else {
                foreach ($allTransactionsDetailsInProgress as $savedTransactionDetail) {
                    if ($savedTransactionDetail["id"] === $transactionDetail["id"]) {
                        $transactionAlreadyCounted =  false;
                    }
                }
        
                $transactionAlreadyCounted = true;
            }

            if ($transactionDetail["status"] === TransactionHistoryStatus::FINISHED &&
                !$transactionAlreadyCounted
            ) {
                $master->successCount = $transactionHistoryMaster["successCount"] + 1;
            } else if ($transactionDetail["status"] === TransactionHistoryStatus::FAILED &&
                !$transactionAlreadyCounted
            ) {
                $master->failedCount = $transactionHistoryMaster["failedCount"]+ 1;
            } else if ($transactionDetail["status"]  === TransactionHistoryStatus::FILTERED_OUT &&
                !$transactionAlreadyCounted
            ) {
                $filteredOutCount++;
            }
        }

        if ($failedCountBeforeUpdate < $transactionHistoryMaster["failedCount"]) {
            $transactionEntityName = ($type === FilterType::IMPORT_PRICES) ? 'prices' : 'products';
            $newNote = $transactionHistoryMaster["failedCount"] . ' of ' .
                $transactionHistoryMaster["totalCount"] . ' ' . $transactionEntityName  . ' failed.';

              $master->note = $newNote;              
        }

        if ($filteredOutCount > 0) {
            $newNote = $filteredOutCount . ' of ' .
                $transactionHistoryMaster["totalCount"] . ' prices ' . ' filtered out.';
            $master->note = $newNote;
        }

        if (!empty($uniqueIdentifier) && $transactionHistoryMaster["uniqueIdentifier"] === null) {
            $master->uniqueIdentifier = $uniqueIdentifier;
        }

        $database->save($master);
    }

}