<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class ProductFilter
 *
 * @property int     $id
 * @property string  $contractId
 * @property  string  $type
 * @property string  $serializedFilter
 */
class ProductFilter extends Model
{
    
    public $id = 0;
    /**
     * @var string
     */
    public $contractId    = "";
    public $type          = "";
    public $serializedFilter  = "";

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::ProductFilter';
    }
}