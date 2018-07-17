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
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;


 /**
  * Class AccountController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class AccountController extends Controller
 {
     use Loggable;
   
    /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

         /**
         *
         * @var ConfigRepository
         */
        private $config;

        /**
         *
         * @var ContractRepositoryContract
         */
        private $contractRepo;

          /**
         *
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;


    public function __construct(PriceMonitorSdkService $sdkService,ConfigRepository $config,ContractRepositoryContract $contractRepo,ConfigRepositoryContract $configInfoRepo)
    {
        $this->sdkService = $sdkService;
        $this->config = $config;
        $this->contractRepo = $contractRepo;
        $this->configInfoRepo = $configInfoRepo;      
    }

    public function getAccountInfo()
    {
        // $emailObject = $this->configInfoRepo->getConfig('email');
        // $passwordObject = $this->configInfoRepo->getConfig('password');
        // $transactionsRetentionIntervalObject = $this->configInfoRepo->getConfig('transactionsRetentionInterval');
        // $transactionDetailsRetentionIntervalObject = $this->configInfoRepo->getConfig('transactionDetailsRetentionInterval');
    
        // $email = $emailObject->value;
        // $password = $passwordObject->value;
        // $transactionsRetentionInterval = $transactionsRetentionIntervalObject->value;
        // $transactionDetailsRetentionInterval = $transactionDetailsRetentionIntervalObject->value;

        // $data = array(
        //     'userEmail' =>   $email,
        //     'userPassword' => $password,
        //     'transactionsRetentionInterval' => $transactionsRetentionInterval,
        //     'transactionDetailsRetentionInterval' => $transactionDetailsRetentionInterval
        // );

        $accountInfo = $this->sdkService->call("getAccountInfoFromMiddleware", []);

        return json_encode($accountInfo);
    }

    public function saveAccountInfo(Request $request) 
    {
        $requestData = $request->all();

        if($requestData == null)
            throw new \Exception("Request data are empty!");
        
        $email = $requestData['email'];
        $password = $requestData['password'];
        $transactionsRetentionInterval = $requestData['transactionsRetentionInterval'];
        $transactionDetailsRetentionInterval = $requestData['transactionDetailsRetentionInterval'];

        $saveAccountInfoToMiddleware = $this->sdkService->call("saveAccountInfoToMiddleware", [
            'email' => $email,
            'password' => $password,
            'transactionsRetentionInterval' => $transactionsRetentionInterval,
            'transactionDetailsRetentionInterval' => $transactionDetailsRetentionInterval 
        ]);

        if( $saveAccountInfoToMiddleware != null &&  isset($saveAccountInfoToMiddleware['error']))
        {
              $errorReponse = [
                'Message' => $saveAccountInfoToMiddleware['error']
             ];

            return  $errorReponse;
        }
            
        return "Account information saved successfully!";
    }
 }