<?php

require_once __DIR__ . '/RunnerService.php';

use PriceMonitorPlentyIntegration\Services\ScheduleExportService;

try {

    // $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    // $queueModel = SdkRestApi::getParam('queueModel');

    // $runnerService = new RunnerService($queueModel);

    // $runnerService->enqueueProductExportJob($priceMonitorId);
    
    // $runnerService->runAsync();
    $scheduleExportService = pluginApp(ScheduleExportService::class);

    $scheduleSaved = $scheduleExportService->getAdequateScheduleByContract($priceMonitorId);


} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>