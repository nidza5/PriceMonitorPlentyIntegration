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
}