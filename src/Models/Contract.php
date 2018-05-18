<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class Contract
 *
 * @property int     $id
 * @property string  $priceMonitorId
 * @property string  $name
 * @property int     $salesPriceImportInId
 * @property bool     $isInsertSalesPrice
 */
class Contract extends Model
{
    
    public $id                = 0;
    /**
     * @var string
     */
    public $priceMonitorId    = "";
    public $name              = "";
    public $salesPriceImportInId   = 0;
    public $isInsertSalesPrice = false;


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::Contract';
    }
}