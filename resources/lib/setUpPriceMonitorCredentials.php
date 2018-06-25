<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

$email = SdkRestApi::getParam('email');
$password = SdkRestApi::getParam('password');

$configService = SdkRestApi::getParam('configService');

PriceMonitorSdkHelper::registerConfigService($email,$password,$configService);

 PriceMonitorSdkHelper::setUpCredentials($email,$password,$configService);

 return true;
?>