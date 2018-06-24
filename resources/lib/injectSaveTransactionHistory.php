<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/TransactionStorage.php';

  $savedMasterTransactionHistory = SdkRestApi::getParam('savedTransactionMasterRecord');

  $contractId = SdkRestApi::getParam('priceMonitorId');
  $storage =  new TransactionStorage();

  return  $storage->startTransactionExport($contractId);
  
?>