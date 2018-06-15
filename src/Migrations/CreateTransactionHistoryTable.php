<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\TransactionHistory;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateTransactionHistoryTable
 */
class CreateTransactionHistoryTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(TransactionHistory::class);
 
        $this->getLogger("CreateTransactionHistoryTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'TransactionHistory']);
 
    }
}