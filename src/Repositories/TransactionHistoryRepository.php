<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
use PriceMonitorPlentyIntegration\Models\TransactionHistory;
 
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
        $transactionHistory = $database->query(TransactionHistory::class)->get();
        
        if($data['uniqueIdentifier'] != null) {
            $transactionHistory->uniqueIdentifier = $data['uniqueIdentifier'];            
        }    
        
        if($data['time'] != null) {
            $transactionHistory->time = $data['time']; 
        }

        $transactionHistory->status = $data['status']; 
        $transactionHistory->note = $data['note']; 
        $transactionHistory->totalCount = $data['totalCount']; 
        $transactionHistory->successCount = $data['successCount']; 
        $transactionHistory->failedCount = $data['failedCount']; 
        $transactionHistory->type = $data['type']; 
        $transactionHistory->priceMonitorContractId = $data['contractId']; 

        $database->save($transactionHistory);

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
}