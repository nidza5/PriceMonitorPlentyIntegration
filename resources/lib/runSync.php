<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {

   $queueModel = SdkRestApi::getParam('queueModel');
   $queueName = SdkRestApi::getParam('queueName');
   
   $emailForConfig = SdkRestApi::getParam('emailForConfig');
   $passwordForConfig = SdkRestApi::getParam('passwordForConfig');

   $filterType = SdkRestApi::getParam('filterType');
   $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    return $priceMonitorId;
//    $productFilterRepo = SdkRestApi::getParam('productFilterRepo');

//    $products = SdkRestApi::getParam('products');

//    $productsAttributes = SdkRestApi::getParam('attributesFromPlenty');
//    $attributeMapping = SdkRestApi::getParam('attributeMapping');
//    $contract = SdkRestApi::getParam('contract');  

//    $savedInitialTransaction = SdkRestApi::getParam('savedTransactionMasterRecord');    
  
//    PriceMonitorSdkHelper::registerConfigService($emailForConfig,$passwordForConfig);

//    PriceMonitorSdkHelper::registerMapperService($attributeMapping,$contract,$products,$productsAttributes); 
//    PriceMonitorSdkHelper::registerProductService($contract,$products); 

//     PriceMonitorSdkHelper::registerTransactionHistotyStorage(null,0,null,0,$savedInitialTransaction);

//    $savedMasterTransactionHistory = SdkRestApi::getParam('savedTransactionMasterRecord');
   

//    $runnerService = new RunnerService($queueModel);
//    $runnerService->enqueueProductExportJob($priceMonitorId);
//    return  $runnerService->runSync($queueName,$products,$savedInitialTransaction['id'], $priceMonitorId);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>