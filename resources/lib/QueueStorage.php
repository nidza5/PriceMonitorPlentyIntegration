<?php

use Patagona\Pricemonitor\Core\Interfaces\Queue\Storage;
use Patagona\Pricemonitor\Core\Sync\Queue\StorageModel;

class QueueStorage implements Storage
{

    private $queueModel;

    public function __construct($queueModel)
    {
        $this->queueModel = $queueModel;
    }

    /**
     * @inheritdoc
     */
    public function peek($queueName)
    {
        return $this->getStorageModel($queueName, false);
    }

    /**
     * @inheritdoc
     */
    public function lock($queueName)
    {
        return $this->getStorageModel($queueName, true);
    }

    /**
     * @inheritdoc
     */
    public function save($queueName, $storageModel)
    {
        try {
            $result = $this->setStorageModel($queueName, $storageModel);
        } catch (Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function delete($queueName, $storageModel)
    {
        try {
            $result = $this->unsetStorageModel($queueName, $storageModel);
        } catch (Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function beginTransaction()
    {
        
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
       
    }

    /**
     * @inheritdoc
     */
    public function rollBack()
    {
      
    }

    /**
     * @param string $queueName
     * @param bool $lock
     *
     * @return StorageModel
     */
    protected function getStorageModel($queueName, $lock = false)
    {
        $queueItem = $this->queueModel;

        if ($queueItem == null) {
            return null;
        }

        $reservationTime = $queueItem["reservationTime"] === null ? "" :
            DateTime::createFromFormat('Y-m-d H:i:s', $queueItem["reservationTime"]);

        $storageModel = new StorageModel(
            array(
                'id' => $queueItem['id'],
                'reservationTime' => $reservationTime,
                'attempts' => $queueItem['attempts'],
                'payload' => $queueItem['payload']
            )
        );
       return  serialize($storageModel);

        // return new StorageModel(
        //     array(
        //         'id' => $queueItem['id'],
        //         'reservationTime' => $reservationTime,
        //         'attempts' => $queueItem['attempts'],
        //         'payload' => $queueItem['payload']
        //     )
        // );
    }

    /**
     * Saves or updates queue job.
     *
     * @param string $queueName
     * @param StorageModel $storageModel
     *
     * @return bool
     * @throws Exception
     */
    protected function setStorageModel($queueName, $storageModel)
    {
       //return parameter to save in src folder
       // within lib there is no way to do insert in database

       $reservationTime = $storageModel->getReservationTime() !== null ?
            $storageModel->getReservationTime()->format('Y-m-d H:i:s') : null;
            
       $storageModelArray = [
            "id" => $storageModel->getId(),
            "reservationTime" => $reservationTime,
            "attempts" => $storageModel->getAttempts() ,
            "payload" => $storageModel->getPayload()
       ];

       return ['queueName' => $queueName,
               'storageModel' => $storageModelArray];
    }

    /**
     * Deletes storage model by ID
     *
     * @param string $queueName
     * @param StorageModel $storageModel
     *
     * @return bool
     * @throws Exception
     */
    protected function unsetStorageModel($queueName, $storageModel)
    {
        
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function getConnection()
    {
        
        // $resource = Mage::getSingleton('core/resource');
        // return $resource->getConnection('default_write');
    }

}