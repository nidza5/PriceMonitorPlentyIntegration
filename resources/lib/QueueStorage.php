<?php

use Patagona\Pricemonitor\Core\Interfaces\Queue\Storage;
use Patagona\Pricemonitor\Core\Sync\Queue\StorageModel;

class QueueStorage implements Storage
{
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
        $this->getConnection()->beginTransaction();
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        $this->getConnection()->commit();
    }

    /**
     * @inheritdoc
     */
    public function rollBack()
    {
        $this->getConnection()->rollBack();
    }

    /**
     * @param string $queueName
     * @param bool $lock
     *
     * @return StorageModel
     */
    protected function getStorageModel($queueName, $lock = false)
    {
        /** @var Patagona_Pricemonitor_Model_Resource_Queue_Collection $queueCollection */
        $queueCollection = Mage::getResourceModel('pricemonitor/queue_collection');
        $queueCollection->filterByQueueName($queueName, $lock);

        /** @var Patagona_Pricemonitor_Model_Queue $queueItem */
        $queueItem = $queueCollection->setPageSize(1)->getLastItem();

        if (!$queueItem || !$queueItem->getId()) {
            return null;
        }

        $reservationTime = $queueItem->getReservationTime() === null ? null :
            DateTime::createFromFormat('Y-m-d H:i:s', $queueItem->getReservationTime());

        return new StorageModel(
            array(
                'id' => $queueItem->getId(),
                'reservationTime' => $reservationTime,
                'attempts' => $queueItem->getAttempts(),
                'payload' => $queueItem->getPayload()
            )
        );
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
        /** @var Patagona_Pricemonitor_Model_Queue $queueModel */
        $queueModel = Mage::getModel('pricemonitor/queue');

        if ($storageModel->getId() === null) {
            $queueModel->setQueueName($queueName);
        } else {
            $queueModel = $queueModel->getQueueByIdAndName($storageModel->getId(), $queueName);

            if (!$queueModel->getId()) {
                return false;
            }
        }

        $reservationTime = $storageModel->getReservationTime() !== null ?
            $storageModel->getReservationTime()->format('Y-m-d H:i:s') : null;

        $queueModel->setReservationTime($reservationTime);
        $queueModel->setAttempts($storageModel->getAttempts());
        $queueModel->setPayload($storageModel->getPayload());
        $queueModel->save();
        return true;
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
        /** @var Patagona_Pricemonitor_Model_Queue $queueModel */
        $queueModel = Mage::getModel('pricemonitor/queue');
        /** @var Patagona_Pricemonitor_Model_Queue $queue */
        $queue = $queueModel->getQueueByIdAndName($storageModel->getId(), $queueName);

        if (!$queue->getId()) {
            return false;
        }

        $queue->delete();
        return true;
    }

    /**
     * @return Varien_Db_Adapter_Interface
     */
    protected function getConnection()
    {
        /** @var \Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('default_write');
    }

}