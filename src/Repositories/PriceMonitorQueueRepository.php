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
    public function savePriceMonitorQueue($queueName,array $data)
    {
      $database = pluginApp(DataBase::class);
     
      $queueModel = pluginApp(PriceMonitorQueue::class);

      if($data['id'] === null || $data['id'] === 0)
      {
          $queueModel->queueName = $queueName;
      } else {
          $queueModel = $this->getQueueByIdName($data['id'],$queueName);

          if($queueModel->id === 0 || $queueModel->id === null)
              return false;
      }

        $reservationTime = $data['reservationTime'] != null ?
                $data['reservationTime']->format('Y-m-d H:i:s') : "";
        
        $queueModel->reservationTime = $reservationTime;
        $queueModel->attempts = $data['attempts'] !== null ? $data['attempts'] : "";
        $queueModel->payload = $data['payload'] !== null ? $data['payload'] : "";

        $database->save($queueModel);
    }
    
    public function getQueueByName($queueName)
    {
        $databaseQueue = pluginApp(DataBase::class);
        $queueOriginal = $databaseQueue->query(PriceMonitorQueue::class)->where('queueName', '=', $queueName)->orderBy("id","asc")->get();

        if($queueOriginal == null)
        return pluginApp(PriceMonitorQueue::class);

      return $queueOriginal[0];
      
    }

    public function getQueueByIdName($id,$queueName)
    {
        $databaseQueue = pluginApp(DataBase::class);
        $queueOriginal = $databaseQueue->query(PriceMonitorQueue::class)->where('id', '=', $id)->where('queueName', '=', $queueName)->get();

        if($queueOriginal == null)
        return pluginApp(PriceMonitorQueue::class);

      return $queueOriginal[0];
      
    }

    public function deleteAllQueue() 
    {
        $database = pluginApp(DataBase::class);
 
        $queueList = $database->query(PriceMonitorQueue::class)->get();
 
        foreach($queueList as $con)
        {
            $database->delete($con);
        }
    }

    public function deleteQueue($queueName, array $storageModel)
    {
        $database = pluginApp(DataBase::class);
        
        $queue = $this->getQueueByIdName($storageModel["id"],$queueName);

        if($queue != null)
             $database->delete($queue);
    }

    public function updateReservationTime($queueName, array $storageModel)
    {
        $database = pluginApp(DataBase::class);
        
        $queue = $this->getQueueByIdName($storageModel["id"],$queueName);

        if($queue != null) {
            $queue->reservationTime = null;
            $database->save($queue);
        }

    }
}