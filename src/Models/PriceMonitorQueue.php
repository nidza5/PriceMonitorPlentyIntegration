<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class PriceMonitorQueue
 *
 * @property int     $id
 * @property string  $queueName
 * @property string  $reservationTime
 * @property string  $attempts
 * @property string  $payload
 */
class PriceMonitorQueue extends Model
{
    
    public $id = 0;
    public $queueName   = "";
    public $reservationTime = "";
    public $attempts   = "";
    public $payload = "";

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::PriceMonitorQueue';
    }
}