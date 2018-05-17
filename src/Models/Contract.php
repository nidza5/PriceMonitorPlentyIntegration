<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class Contract
 *
 * @property int     $id
 * @property int     $priceMonitorId
 * @property string  $name
 * @property int     $salesPriceImportInId
 * @property bool     $isInsertSalesPrice
 */
class Contract extends Model
{
    /**
     * @var int
     */
    public $id                = 0;
    public $priceMonitorId    = 0;
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