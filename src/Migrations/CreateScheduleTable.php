<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\Schedule;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateScheduleTable
 */
class CreateScheduleTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(Schedule::class);
 
        $this->getLogger("CreateScheduleTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'Schedule']);
 
    }
}