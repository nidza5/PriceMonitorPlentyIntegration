<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\RunnerToken;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateRunnerTokenTable
 */
class CreateRunnerTokenTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(RunnerToken::class);
 
        $this->getLogger("CreateRunnerTokenTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'CreateRunnerTokenTable']);
 
    }
}