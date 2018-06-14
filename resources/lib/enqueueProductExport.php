<?php

require_once __DIR__ . '/RunnerService.php';


$priceMonitorId = SdkRestApi::getParam('priceMonitorId');

$runnerService = new RunnerService();

return $runnerService->enqueueProductExportJob($priceMonitorId);


?>