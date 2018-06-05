<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

require_once __DIR__ . '/ConfigurationService.php';

$filterData = SdkRestApi::getParam('filterData');

$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

return PriceMonitorSdkHelper::saveFilter($filterData,$filterType,$priceMonitorId);

?>