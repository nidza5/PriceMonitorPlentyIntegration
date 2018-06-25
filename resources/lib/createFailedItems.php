<?php

require_once __DIR__ . '/RunnerService.php';
require_once __DIR__ . '/PriceMonitorSdkHelper.php';

use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionFailedDTO;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryStatus;

try {

    $notImportPrices = SdkRestApi::getParam('notImportPrices');

    $failedItems = [];

    foreach ($notImportPrices as $errorProduct) {
        $failedItem = new TransactionFailedDTO(
            $errorProduct['productId'],
            implode(' ', $errorProduct['errors']),
            $errorProduct['status'],
            isset($errorProduct['name']) ? $errorProduct['name'] : null,
            null,
            null,
            null
        );
        $failedItems[] = $failedItem;
    }

    return $failedItems;
  

} catch(\Exception $ex) {
    
    $response = [
        'Code' => $ex->getCode(),
        'Message' => $ex->getMessage()
     ];

    return $response;
}

?>