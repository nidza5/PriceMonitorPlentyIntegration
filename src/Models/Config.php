<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class Config
 *
 * @property int     $id
 * @property string  $key
 * @property string  $value
 */
class Config extends Model
{
    
    public $id = 0;
    public $key    = "";
    public $value = "";

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::Config';
    }
}