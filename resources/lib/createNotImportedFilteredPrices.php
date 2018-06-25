<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/MapperService.php';

use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryStatus;

try {

   $filterType = SdkRestApi::getParam('filterType');
   $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

   $productsAttributes = SdkRestApi::getParam('attributesFromPlenty');
   $attributeMapping = SdkRestApi::getParam('attributeMapping');
   $contract = SdkRestApi::getParam('contract');  

   $batchPrices = SdkRestApi::getParam('batchPrices');
   $filteredShopProducts = SdkRestApi::getParam('filteredShopProducts');

   $transactionDetails = SdkRestApi::getParam('transactionDetails');

   $mapper = new MapperServices($attributeMapping,$contract,$filteredShopProducts,$productsAttributes);
  
   $notImportedPrices = [];

   foreach ($batchPrices as $batchPriceIndex => &$batchPrice) {
       foreach ($transactionDetails as &$transactionDetail) {
           if ($batchPrice['identifier'] == $transactionDetail["id"]) {
               $transactionDetail["productName"] = $batchPrice['name'];
           }
       }
   }

   if (count($filteredShopProducts) === 0) {
       return [];
   }

   foreach ($batchPrices as $batchPriceIndex => &$batchPrice) {
       $productShouldBeImported = false;
       
       foreach ($filteredShopProducts as $filteredProduct) {
           if (!isset($batchPrice['identifier'])) {
               continue;
           }

           $ids = is_array($filteredProduct) ? array_values($filteredProduct) : null;
           $productId = !empty($ids[0]) ? $ids[0] : null;

           $filteredProduct = $this->mapperService->convertToPricemonitor($priceMonitorId, $filteredProduct);

           if (!empty($filteredProduct['productId'])) {
               $productId = $filteredProduct['productId'];
           }

           if ($batchPrice['identifier'] == $productId) {
               $productShouldBeImported = true;
               break;
           }
       }

       if (!$productShouldBeImported) {
           $notImportedPrices[] = [
               'productId' => $batchPrice['identifier'],
               'errors' => [],
               'name' => $batchPrice['name'],
               'status' => TransactionHistoryStatus::FILTERED_OUT
           ];
       }
   }

   return $notImportedPrices;


} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>