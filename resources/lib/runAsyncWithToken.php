<?php

require_once __DIR__ . '/RunnerService.php';

try {

   $runnerService = new RunnerService();
   
   return  $runnerService->runAsync();

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>