<?php
 
namespace PriceMonitorPlentyIntegration\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class Schedule
 *
 * @property int     $id
 * @property boolean  $enableExport
 * @property boolean  $enableImport
 * @property string  $exportStart
 * @property int  $exportInterval
 * @property string  $nextStart
 * @property int  $contractId
 */
class Schedule extends Model
{
    
    public $id = 0;
    public $enableExport  = false;
    public $enableImport = false;
    public $exportStart   = "";
    public $exportInterval = 0;
    public $nextStart = "";
    public $contractId = 0;


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'PriceMonitorPlentyIntegration::Schedule';
    }
}