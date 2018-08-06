<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';
 
$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$email = SdkRestApi::getParam('email');

$password = SdkRestApi::getParam('password');

$host = SdkRestApi::getParam('host');

$tenantId = SdkRestApi::getParam('tenantId');

$client = new PriceMonitorHttpClient();
$res = $client->request(
    'GET',
    $middlewareBaseUrl.'/api/login?email='.$email.'&password='.$password.'&host='.$host.'&tenantId='.$tenantId
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>