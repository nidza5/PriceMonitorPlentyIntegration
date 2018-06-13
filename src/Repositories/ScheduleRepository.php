<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Models\Schedule;
 
class ScheduleRepository implements ScheduleRepositoryContract
{
      /**
     * Save saveSchedule
     *
     * @param array $data
     * @return void
     */
    public function saveSchedule(array $data) 
    {

    }
    
    /**
     *  Schedule
     *
     * @return Schedule
     */
    public function getScheduleByContractId($contractId): Schedule
    {
        $database = pluginApp(DataBase::class);
        $schedule = pluginApp(Schedule::class);

        return $schedule;
    }
}