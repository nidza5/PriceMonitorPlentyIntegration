<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\Config;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateConfigTable
 */
class CreateConfigTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(Config::class);
 
        $this->getLogger("CreateAccountInfoTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'Config']);
 
    }
}