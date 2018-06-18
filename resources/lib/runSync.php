<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/ConfigService.php';

try {

  
   ServiceRegister::registerConfigService(new ConfigService());

   $queueModel = SdkRestApi::getParam('queueModel');
   $queueName = SdkRestApi::getParam('queueName');
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