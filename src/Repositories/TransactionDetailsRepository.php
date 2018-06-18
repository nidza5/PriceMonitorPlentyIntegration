<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\TransactionDetailsRepositoryContract;
use PriceMonitorPlentyIntegration\Models\TransactionDetails;
 
class TransactionDetailsRepository implements TransactionDetailsRepositoryContract
{
     /**
     * Save
     *
     * @param array $data
     * @return void
     */
    public function saveTransactionDetails(array $data)
    {
        
    }

    public function getAllTransactionDetails()
    {
        $database = pluginApp(DataBase::class);
        $transactionDetails = $database->query(TransactionDetails::class)->get();
        
        return $transactionDetails;
    }

    public function getTransactionHistoryDetailsCount($masterId)
    {
        $database = pluginApp(DataBase::class);
        $transactionDetailsCount = $database->query(TransactionDetails::class)->where('transactionId', '=', $masterId)->count();
        
        return $transactionDetailsCount;
    }
}