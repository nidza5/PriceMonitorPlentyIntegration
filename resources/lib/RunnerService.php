<?php

require_once __DIR__ . '/QueueStorage.php';
require_once __DIR__ . '/TransactionStorage.php';
require_once __DIR__ . '/PriceMonitorHttpClient.php';

use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
use Patagona\Pricemonitor\Core\Sync\PriceImport\Job as PriceImportJob;
use Patagona\Pricemonitor\Core\Sync\ProductExport\Job as ProductExportJob;
use Patagona\Pricemonitor\Core\Sync\Queue\Queue;
use Patagona\Pricemonitor\Core\Sync\Runner\Runner;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryDetail;
use Patagona\Pricemonitor\Core\Sync\StatusCheck\Job as StatusCheckJob;

class RunnerService
{
    const DEFAULT_QUEUE_NAME = 'Default';
    const STATUS_CHECKING_QUEUE_NAME = 'StatusChecking';

    /**
     * It should be enabled only in development mode
     *
     * @var bool
     */
    protected static $_isDebugEnabled = false;
    /**
     * Xdebug key
     *
     * @var string
     */
    protected static $_debugKey = '';

    public function __construct($queueModel=null)
    {
        ServiceRegister::registerQueueStorage(new QueueStorage($queueModel));
        ServiceRegister::registerTransactionHistoryStorage(new TransactionStorage());
        ServiceRegister::registerHttpClient(new PriceMonitorHttpClient());
    }

    /**
     * Creates new export product job.
     *
     * @return void
     * @param $pricemonitorContractId
     */
    public function enqueueProductExportJob($pricemonitorContractId)
    {
        $queue = new Queue();
        return  $queue->enqueue(new ProductExportJob($pricemonitorContractId));
    }



    /**
     * Creates new import price job.
     *
     * @return void
     * @param $pricemonitorContractId
     */
    public function enqueuePriceImportJob($pricemonitorContractId)
    {
        $queue = new Queue();
        $queue->enqueue(new PriceImportJob($pricemonitorContractId));
    }

    private function enqueueStatusCheckerJob($exportTaskId,$contractId)
    {
        $queue = new Queue('StatusChecking');
        $queue->enqueue(new StatusCheckJob($contractId, $exportTaskId));
    }

    /**
     * Runs sync request.
     *
     * @param string $queueName If not provided, default queue name will be used.
     *
     * @return void
     * @throws Exception
     */
    public function runSync($queueName = null,$products,$transactionId,$priceMonitorId)
    {
        $queueName = $queueName === null ? self::DEFAULT_QUEUE_NAME : $queueName;

        $runner = new Runner($queueName);
        $arraysResultRun =  $runner->run();

        $productsForExport = null;

        if($products != null) {
            $mapperService = new MapperServices(null,null,$products,null);

            foreach ($products as $shopProduct) {
                $product = $mapperService->convertToPricemonitor($priceMonitorId, $shopProduct);
               // $this->logProductValidationWarnings($product);
                $productsForExport[] = $product;
            }
        }
        
        $transactionHistoryDetailsForSaving = $this->createTransactionDetails($transactionId, $productsForExport);
        
         $returnArray = [
             "arrayUniqueIdentifier"  => $arraysResultRun["UniqueIdentifiers"],
             "transactionHistoryDetailsForSaving" => $transactionHistoryDetailsForSaving,
             "dequeus" => $arraysResultRun["Dequeu"],
             "release" => $arraysResultRun["Release"]
         ];

         return $returnArray; 
     }
         #
        // $this->runAsync($queueName);
        // if ($queueName === self::DEFAULT_QUEUE_NAME) {
        //     $this->runAsync(self::STATUS_CHECKING_QUEUE_NAME);
        // }
  
    public function createTransactionDetails($transactionId, $products)
    {
        /** @var TransactionHistoryDetail[] $transactionDetails */
        $transactionDetails = [];
        
        foreach ($products as $product) {
            /** @var TransactionHistoryDetail $transactionDetail */
            $transactionDetail = new TransactionHistoryDetail(
                TransactionHistoryStatus::IN_PROGRESS, 
                new \DateTime(),
                null,
                $transactionId, 
                null,
                isset($product['productId']) ? $product['productId'] : null,
                isset($product['gtin']) ? $product['gtin'] : null,
                isset($product['name']) ? $product['name']: null,
                isset($product['referencePrice']) ? (float)$product['referencePrice'] : null,
                isset($product['minPriceBoundary']) ? (float)$product['minPriceBoundary'] : null,
                isset($product['maxPriceBoundary']) ? (float)$product['maxPriceBoundary'] : null
            );

            $transactionDetail->setUpdatedInShop(false);

            $transactionDetails[] = $transactionDetail; 
        }

        return $transactionDetails;
    }

    /**
     * Runs async request.
     *
     * @param string $queueName If not provided, default queue name will be used.
     *
     * @return void
     * @throws Exception
     */
    public function runAsync($queueName = null)
    {
        $queueName = $queueName === null ? self::DEFAULT_QUEUE_NAME : $queueName;

        // check queue first. If queue is empty run is complete, just skip.
        $queue = ServiceRegister::getQueueStorage()->peek($queueName);

        if (!empty($queue)) {
           // $runnerToken = $this->createRunnerToken();
            //$this->callAsyncRequest($runnerToken->getToken(), $queueName);

            return ['isCreateRunnerToken' => true,
                    'queueName' => $queueName ];
        }
        else {
            return ['isCreateRunnerToken' => false,
                    'queueName' => $queueName ];
        }
    }

    /**
     * Creates new runner token.
     *
     * @return Token
     * @throws Exception
     */
    protected function createRunnerToken()
    {
        // /** @var Mage_Core_Helper_Data $coreHelper */
        // $coreHelper = Mage::helper('core');
        // /** @var Patagona_Pricemonitor_Model_RunnerToken $runnerToken */
        // $runnerToken = Mage::getModel('pricemonitor/runnerToken');
        // $runnerToken->setToken($coreHelper->uniqHash('pricemonitor_'));
        // return $runnerToken->save();
    }

    /**
     * Calls task runner via async request.
     *
     * @param string $token Secret token used for request validation.
     * @param string $queueName Queue name, possible values are defined in constants
     *                          DEFAULT_QUEUE_NAME and STATUS_CHECKING_QUEUE_NAME
     *
     * @throws Mage_Core_Exception
     */
    protected function callAsyncRequest($token, $queueName)
    {
        // /** @var Mage_Core_Model_Url $urlModel */
        // $urlModel = Mage::getModel('core/url');
        // // when configuration "Web"->"Add Store Code to Urls" is turned on,
        // // frontend store code must be provided in url
        // $defaultStore = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();

        // $url = $urlModel->setStore($defaultStore)->getUrl(
        //     'pricemonitor/sync/run',
        //     array('queueName' => $queueName, 'token' => $token)
        // );

        // if (self::$_isDebugEnabled === true) {
        //     $url .= '?XDEBUG_SESSION_START=' . self::$_debugKey;
        // }

        // ServiceRegister::getHttpClient()->requestAsync('GET', $url);
    }

}