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
    public function saveTransactionHistory(array $data)
    {
        
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