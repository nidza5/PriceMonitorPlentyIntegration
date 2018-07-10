<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

return $middlewareBaseUrl;

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl +'/api/login?email=goran.stamenkovski@logeecom.com&password=Goran'
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>