<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\Schedule;
 
/**
 * Class ScheduleRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface ScheduleRepositoryContract
{
    /**
     * Save saveSchedule
     *
     * @param array $data
     * @return void
     */
    public function saveSchedule(array $data);

 
    /**
     *  Schedule
     *
     * @return Schedule
     */
    public function getScheduleByContractId($contractId): Schedule;

}