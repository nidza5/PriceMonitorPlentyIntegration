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
}