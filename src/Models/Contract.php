<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class Contract
 *
 * @property int     $id
 * @property int     $priceMonitorId
 * @property string  $name
 * @property int     $customerGroupId
 * @property int     $shopImportPriceId
 */
class Contract extends Model
{
    /**
     * @var int
     */
    public $id                = 0;
    public $priceMonitorId    = 0;
    public $name              = "";
    public $customerGroupId   = 0;
    public $shopImportPriceId = 0;


    public function __construct($id,$priceMonitorId,$name,$customerGroupId = 0,$shopImportPriceId = 0) {

        $this->id = $id;
        $this->priceMonitorId = $priceMonitorId;
        $this->name = $name;
        $this->customerGroupId = $customerGroupId;
        $this->shopImportPriceId = $shopImportPriceId;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::Contract';
    }
}