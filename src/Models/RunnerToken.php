<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class RunnerToken
 *
 * @property int     $id
 * @property string  $token
 */
class RunnerToken extends Model
{
    
    public $id = 0;
    public $token   = "";

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::RunnerToken';
    }
}