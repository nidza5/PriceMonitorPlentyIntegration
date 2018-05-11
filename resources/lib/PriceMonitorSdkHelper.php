<?php
 
 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;
 use PriceMonitorPlentyIntegration\PriceMonitorHttpClient;

 class PriceMonitorSdkHelper
 {
    public static function loginInPriceMonitor($email,$password)
    {
        new ServiceRegister();

        $client = new PriceMonitorHttpClient();
        ServiceRegister::registerHttpClient($client);

        $proxy = Proxy::createFor($email,$password);      
        $contracts = $proxy->getContracts();
        return $contracts;
    }
 }

?>