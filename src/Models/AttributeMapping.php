<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class AttributeMapping
 *
 * @property int     $id
 * @property string  $attributeCode
 * @property string  $priceMonitorCode
 * @property string  $operand
 * @property string  $value
 * @property int  $contractId
 * @property string  $priceMonitorContractId
 */
class AttributeMapping extends Model
{
    
    public $id = 0;
    public $attributeCode    = "";
    public $priceMonitorCode = "";
    public $operand   = "";
    public $value = "";
    public $contractId = 0;
    public $priceMonitorContractId = "";


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::AttributeMapping';
    }
}