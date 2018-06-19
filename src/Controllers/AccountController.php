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


    public function __construct(PriceMonitorSdkService $sdkService,ConfigRepository $config,ContractRepositoryContract $contractRepo)
    {
        $this->sdkService = $sdkService;
        $this->config = $config;
        $this->contractRepo = $contractRepo;      
    }

    public function getAccountInfo()
    {
        $email = $this->config->get('email');
        $password = $this->config->get('password');
        $transactionsRetentionInterval = $this->config->get('transactionsRetentionInterval');
        $transactionDetailsRetentionInterval = $this->config->get('transactionDetailsRetentionInterval');
    
        $data = array(
            'userEmail' =>   $email,
            'userPassword' => $password,
            'transactionsRetentionInterval' => $transactionsRetentionInterval,
            'transactionDetailsRetentionInterval' => $transactionDetailsRetentionInterval
        );

        return json_encode($data);
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

        $contracts = $this->sdkService->call("getLoginAndContracts", [
            'email' => $email,
            'password' => $password
        ]);

        if($contracts == null || $contracts['error']) 
            throw new \Exception("Contracts doesn't exist or some error occurred!");
            
        $emailFromConfig = $this->config->get('email');
        $passwordFromConfig = $this->config->get('password');
        $transactionsRetentionIntervalFromConfig = $this->config->get('transactionsRetentionInterval');
        $transactionDetailsRetentionIntervalFromConfig = $this->config->get('transactionDetailsRetentionInterval');
        
        if ($email !== $emailFromConfig) {
            $this->contractRepo->saveContracts($contracts);
        }

        $this->config->set('email',$email);
        $this->config->set('password',$password);
        $this->config->set('transactionsRetentionInterval',$transactionsRetentionInterval);
        $this->config->set('transactionDetailsRetentionInterval',$transactionDetailsRetentionInterval);

        return "Account information saved successfully";
    }
 }