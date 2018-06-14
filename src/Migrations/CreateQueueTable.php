<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\PriceMonitorQueue;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateQueueTable
 */
class CreateQueueTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(PriceMonitorQueue::class);
 
        $this->getLogger("CreateQueueTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'PriceMonitorQueue']);
 
    }
}