<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\Contract;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class UpdateContractTable
 */
class UpdateContractTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->updateTable(Contract::class);
 
        $this->getLogger("UpdateContractTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'Contract']);
 
    }
}