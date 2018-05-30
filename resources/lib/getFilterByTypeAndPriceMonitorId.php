<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$productFilterRepo = SdkRestApi::getParam('productFilterRepo');

return PriceMonitorSdkHelper::getFilter($filterType,$priceMonitorId,$productFilterRepo);

?>