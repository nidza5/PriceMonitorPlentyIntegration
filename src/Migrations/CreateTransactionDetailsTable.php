<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\TransactionDetails;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateTransactionDetailsTable
 */
class CreateTransactionDetailsTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(TransactionDetails::class);
 
        $this->getLogger("CreateTransactionDetailsTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'TransactionDetails']);
 
    }
}