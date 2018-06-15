<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class TransactionDetails
 *
 * @property int     $id
 * @property string  $time
 * @property string  $productId
 * @property string  $status
 * @property string  $gtin
 * @property string  $productName
 * @property string  $note
 * @property boolean  $isUpdated
 * @property string  $referencePrice
 * @property string  $minPrice
 * @property string  $maxPrice
 * @property int  $transactionId
 * @property int  $transactionUniqueIdentifier
 */
class TransactionDetails extends Model
{
    
    public $id = 0;
    public $time = "";
    public $productId   = "";
    public $status = "";
    public $gtin = "";
    public $productName = "";
    public $note = "";
    public $isUpdated = false;
    public $referencePrice = "";
    public $minPrice = "";
    public $maxPrice = "";
    public $transactionId = 0;
    public $transactionUniqueIdentifier = 0;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::TransactionDetails';
    }
}