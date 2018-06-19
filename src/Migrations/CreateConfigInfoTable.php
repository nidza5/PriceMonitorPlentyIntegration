<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\ConfigInfo;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateConfigInfoTable
 */
class CreateConfigInfoTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(ConfigInfo::class);
 
        $this->getLogger("CreateConfigInfoTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'ConfigInfo']);
 
    }
}