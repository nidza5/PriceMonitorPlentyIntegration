<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/PriceMonitorHttpClient.php';

use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
use Patagona\Pricemonitor\Core\Sync\Callbacks\CallbackDTO;
use Patagona\Pricemonitor\Core\Sync\Callbacks\CallbacksSync;
use Patagona\Pricemonitor\Core\Infrastructure\Logger;

try {

//    $token = SdkRestApi::getParam('token');
//    $url = SdkRestApi::getParam('url');
//    $contract_Id = SdkRestApi::getParam('contract_Id');
   
//    $body = array('contract_id' => $contract_Id, 'token' => token);
//    $json = array('Content-Type' => 'application/json');

//    $callbackSync = new CallbacksSync();
//    $refreshPricesCallback = new CallbackDTO('POST', 'PlentyMarketSyncRefreshPrices', $body, $url, $json);

//    try {
//         $callbackSync->registerCallbacks(array($refreshPricesCallback), $contract_Id);
//    } catch (Exception $e) {
//         Logger::logError($e->getMessage());
//         return false;
//     }

$url = SdkRestApi::getParam('url');

$client = new PriceMonitorHttpClient();

$client->request("POST", $url);

 return true;

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>