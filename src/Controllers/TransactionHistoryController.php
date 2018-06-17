<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\ConfigRepository;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;
 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;

 /**
  * Class TransactionHistoryController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class TransactionHistoryController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

    public function __construct(PriceMonitorSdkService $sdkService)
    {
        $this->sdkService = $sdkService;       
    }

    public function getTransactionHistory()
    {

    }

 }