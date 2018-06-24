<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Models\Schedule;
use Plenty\Modules\Cron\Services\CronContainer;
use PriceMonitorPlentyIntegration\Services\ExecuteCron;
 
class ScheduleRepository implements ScheduleRepositoryContract
{
      /**
     * Save saveSchedule
     *
     * @param array $data
     * @return void
     */
    public function saveSchedule($contractId,array $data,CronContainer $cronContainer ) 
    {
        $database = pluginApp(DataBase::class);
     
        $schedule = pluginApp(Schedule::class);
        
        if($contractId != null && $contractId != 0)
            $schedule = $this->getScheduleByContractId($contractId);

        $startAt = $data['startAt'];
        $isEnabledExport = (bool)$data['enableExport'];
        $exportInterval = (int)$data['exportInterval'];

        $schedule->enableExport = $isEnabledExport;
        $schedule->contractId = $contractId;

        if ($isEnabledExport) {
            $schedule->exportStart = $startAt;
            $schedule->nextStart = $startAt;
            $schedule->exportInterval = $exportInterval;
            $cronContainer->add((int)$exportInterval, ExecuteCron::class);
        } else {
            $schedule->exportStart = null;
            $schedule->nextStart = null;
            $schedule->exportInterval = $exportInterval;
        }

        $database->save($schedule);
    }

    public function saveImportSchedule($contractId,array $data)
    { 
        $database = pluginApp(DataBase::class);
     
        $schedule = pluginApp(Schedule::class);
        
        if($contractId != null && $contractId != 0)
            $schedule = $this->getScheduleByContractId($contractId);

         $isEnabled = $data['enableImport'];
         $schedule->enableImport = $isEnabled;
         $schedule->contractId = $contractId;

        $database->save($schedule);

    }
    
    /**
     *  Schedule
     *
     * @return Schedule
     */
    public function getScheduleByContractId($contractId): Schedule
    {
        $databaseSchedule = pluginApp(DataBase::class);
        $scheduleOriginal = $databaseSchedule->query(Schedule::class)->where('contractId', '=', $contractId)->get();

        if($scheduleOriginal == null)
          return pluginApp(Schedule::class);

        return $scheduleOriginal[0];
    }

    public function getAllSchedule() 
    {
        $database = pluginApp(DataBase::class);
        $scheduleList = $database->query(Schedule::class)->get();
        
        return $scheduleList;
    }
}