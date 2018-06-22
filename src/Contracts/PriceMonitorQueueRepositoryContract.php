<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\PriceMonitorQueue;
 
/**
 * Class PriceMonitorQueueRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface PriceMonitorQueueRepositoryContract
{
    /**
     * savePriceMonitorQueue
     *
     * @param array $data
     * @return void
     */
    public function savePriceMonitorQueue($queueName,array $data);

    public function getQueueByName($queueName);

    public function getQueueByIdName($id,$queueName);

    public function deleteAllQueue();

    public function deleteQueue($queueName, array $storageModel);

    public function updateReservationTime($queueName, array $storageModel);
}