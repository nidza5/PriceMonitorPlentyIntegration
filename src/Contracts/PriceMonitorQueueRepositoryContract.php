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
    public function savePriceMonitorQueue(array $data);

    public function getQueueByName($queueName);


}