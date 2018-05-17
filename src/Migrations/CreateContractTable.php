<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\Contract;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateContractTable
 */
class CreateContractTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(Contract::class);
 
        $this->getLogger("CreateContractTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'Contract']);
 
    }
}