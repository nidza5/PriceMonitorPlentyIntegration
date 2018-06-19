<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class ConfigInfo
 *
 * @property int     $id
 * @property string  $key
 * @property string  $value
 */
class ConfigInfo extends Model
{    
    public $id = 0;
    public $key    = "";
    public $value = "";

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::ConfigInfo';
    }
}