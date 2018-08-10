<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Models\ConfigInfo;
 
class ConfigInfoRepository implements ConfigRepositoryContract
{
    public function saveConfig($key, $value)
    {
        /**
         * @var DataBase $database
         */

        if ($key == null || $value == null) {
            return;
        }           
        
        $database = pluginApp(DataBase::class);

        $configValues = pluginApp(ConfigInfo::class);    

        if ($key!= null) {
            $configValues = $this->getConfig($key);
        }           

        $configValues->key = $key;

        $configValues->value = $value;

        $database->save($configValues);
    }

    public function getConfig($key)
    {
        if ($key == null) {
            return pluginApp(ConfigInfo::class);
        }           

        $database = pluginApp(DataBase::class);
        $config = $database->query(ConfigInfo::class)->where('key', '=', $key)->get();
       
        return $config[0] === null ? pluginApp(ConfigInfo::class) : $config[0];
    }
}