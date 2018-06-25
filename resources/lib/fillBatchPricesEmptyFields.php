<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/MapperService.php';

try {

   $filterType = SdkRestApi::getParam('filterType');
   $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

   $productsAttributes = SdkRestApi::getParam('attributesFromPlenty');
   $attributeMapping = SdkRestApi::getParam('attributeMapping');
   $contract = SdkRestApi::getParam('contract');  

   $batchPrices = SdkRestApi::getParam('batchPrices');
   $allProducts = SdkRestApi::getParam('allProducts');

   $mapper = new MapperServices($attributeMapping,$contract,$allProducts,$productsAttributes);
  
   $productIdentifierCode = "id";

    foreach ($batchPrices as $batchPriceIndex => &$batchPrice) {
        $batchPrice['name'] = !empty($batchPrice['name']) ? $batchPrice['name'] : '';
        foreach ($allProducts as $product) {
            $pricemonitorProduct = $mapper->convertToPricemonitor($priceMonitorId, $product);
            if ($product[$productIdentifierCode] === $batchPrice['identifier'] && empty($batchPrice['name'])) {
                $batchPrice['name'] = !empty($pricemonitorProduct['name']) ? $pricemonitorProduct['name'] : '';
                break;
            }
        }
    }

    return $batchPrices;


} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>