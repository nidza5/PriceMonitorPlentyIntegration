<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class TransactionHistory
 *
 * @property int     $id
 * @property string  $uniqueIdentifier
 * @property string  $time
 * @property string  $status
 * @property string  $note
 * @property int  $totalCount
 * @property int  $successCount
 * @property int  $failedCount
 * @property string  $type
 * @property string  $priceMonitorContractId
 */
class TransactionHistory extends Model
{
    
    public $id = 0;
    public $uniqueIdentifier  = "";
    public $time = "";
    public $status   = "";
    public $note = "";
    public $totalCount = 0;
    public $successCount = 0;
    public $failedCount = 0;
    public $type = 0;
    public $priceMonitorContractId = "";


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::TransactionHistory';
    }
}