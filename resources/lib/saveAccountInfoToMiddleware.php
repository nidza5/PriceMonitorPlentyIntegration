<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';

$middlewareBaseUrl = SdkRestApi::getParam('gatewayBasePath');

$email = SdkRestApi::getParam('email');
$password = SdkRestApi::getParam('password');
$password = SdkRestApi::getParam('password');
$transactionsRetentionInterval = SdkRestApi::getParam('transactionsRetentionInterval');
$transactionDetailsRetentionInterval = SdkRestApi::getParam('transactionDetailsRetentionInterval');


$client = new PriceMonitorHttpClient();
$res = $client->request(
    'POST',
    $middlewareBaseUrl.'/api/saveAccountInfo',
    [],
    [
        'email' => $email,
        'password' => $password,
        'transactionsRetentionInterval' => $transactionsRetentionInterval,
        'transactionDetailsRetentionInterval' => $transactionDetailsRetentionInterval
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);

?>