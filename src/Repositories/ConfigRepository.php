<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Models\Config;
 
class ConfigRepository implements ConfigRepositoryContract
{
    public function saveConfig($key, $value)
    {
        /**
         * @var DataBase $database
         */

        if($key == null || $value == null)
            return;
        
        $database = pluginApp(DataBase::class);

        $configValues = pluginApp(Config::class);
    

        if($key!= null)
            $configValues = $this->getConfig($key);

        $configValues->key = $key;

        $configValues->value = $value;


        $database->save($configValues);
    }

    public function getConfig($key)
    {
        if($key == null)
            return pluginApp(Config::class);

        $database = pluginApp(DataBase::class);
        $config = $database->query(Config::class)->where('key', '=', $key)->get();
        return $config[0] === null ? pluginApp(Config::class) : $config[0];
    }

}