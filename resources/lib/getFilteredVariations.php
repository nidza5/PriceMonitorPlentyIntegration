<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';

try {

    $filterType = SdkRestApi::getParam('filterType');

    $priceMonitorId = SdkRestApi::getParam('priceMonitorId');

    $productFilterRepo = SdkRestApi::getParam('productFilterRepo');

    $attributeMapping = SdkRestApi::getParam('attributeMapping');

    $allVariations = SdkRestApi::getParam('allVariations');

    $attributesFromPlenty = SdkRestApi::getParam('attributesFromPlenty');
    
  
    return PriceMonitorSdkHelper::getFilteredVariations($filterType, $pricemonitorId, $filterRepo,$attributeMapping,$allVariations,$attributesFromPlenty);

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>