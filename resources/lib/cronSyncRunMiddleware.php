<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/cronSyncRun',
    [],
    []
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>