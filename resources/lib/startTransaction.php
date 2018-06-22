<?php

require_once __DIR__ . '/PriceMonitorSdkHelper.php';
require_once __DIR__ . '/TransactionStorage.php';

  $contractId = SdkRestApi::getParam('contractId');

  $storage =  new TransactionStorage();
  return  $storage->startTransactionExport($contractId);
  
?>