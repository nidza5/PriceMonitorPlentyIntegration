<?php
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;

 class PriceMonitorSdkHelper
 {
    public static function loginInPriceMonitor($email,$password)
    {
        $proxy = Proxy::createFor($email,$password);      
        $contracts = $proxy->getContracts();
        return $contracts;
    }
 }

?>