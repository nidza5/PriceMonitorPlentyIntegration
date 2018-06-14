<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
use PriceMonitorPlentyIntegration\Models\PriceMonitorQueue;
 
class PriceMonitorQueueRepository implements PriceMonitorQueueRepositoryContract
{
      /**
     * savePriceMonitorQueue
     *
     * @param array $data
     * @return void
     */
    public function savePriceMonitorQueue(array $data)
    {
       
    }
    

    public function getQueueByName($queueName)
    {
        $databaseQueue = pluginApp(DataBase::class);
        $queueOriginal = $databaseQueue->query(PriceMonitorQueue::class)->where('queueName', '=', $queueName)->get();

        if($queueOriginal == null)
        return pluginApp(PriceMonitorQueue::class);

      return $queueOriginal[0];
      
    }
}