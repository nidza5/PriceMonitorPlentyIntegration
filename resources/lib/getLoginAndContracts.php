<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

$email = SdkRestApi::getParam('email');
$password = SdkRestApi::getParam('password');

return PriceMonitorSdkHelper::loginInPriceMonitor($email,$password);

?>