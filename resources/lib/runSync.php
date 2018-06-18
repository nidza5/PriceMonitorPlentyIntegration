<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {

   $queueModel = SdkRestApi::getParam('queueModel');
   $queueName = SdkRestApi::getParam('queueName');
   
   $emailForConfig = SdkRestApi::getParam('emailForConfig');
   $passwordForConfig = SdkRestApi::getParam('passwordForConfig');

   PriceMonitorSdkHelper::registerConfigService($emailForConfig,$passwordForConfig);

   $runnerService = new RunnerService($queueModel);
   return  $runnerService->runSync($queueName);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>