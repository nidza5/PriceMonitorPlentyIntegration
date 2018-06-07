<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

// require_once __DIR__ . '/ConfigurationService.php';

$filterData = SdkRestApi::getParam('filterData');

$filterType = SdkRestApi::getParam('filterType');

$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$productFilterRepositoryParam = SdkRestApi::getParam('productFilterRepositoryParam');

return PriceMonitorSdkHelper::saveFilter($filterData,$filterType,$priceMonitorId,$productFilterRepositoryParam);


?>