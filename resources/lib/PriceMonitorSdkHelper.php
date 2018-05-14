<?php
 
 require_once __DIR__ . '/PriceMonitorHttpClient.php';

 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;

 class PriceMonitorSdkHelper
 {
    public static function loginInPriceMonitor($email,$password)
   { 
        try {
            new ServiceRegister();

            $client = new PriceMonitorHttpClient();
            ServiceRegister::registerHttpClient($client);

            $proxy = Proxy::createFor($email,$password);      
            $contracts = $proxy->getContracts();
            echo "isped contracts";
            return $contracts;

        } catch(\Exception $ex)
        {
            // $response = [
            //     'StatusCode' => '500',
            //     'message' => 'Unouthorized access!'
            //  ];

            $response = $ex->getMessage();
            return $response;
        }
    }
 }

?>