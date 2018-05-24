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
 use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
 use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceRepositoryContract;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 /**
  * Class ContentController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class ContentController extends Controller
 {
     use Loggable;

        /**
         *
         * @var ConfigRepository
         */
        private $config;

        /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;

        /**
         *
         * @var SalesPriceRepository
         */
        private $salesPriceRepository;


    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,SalesPriceRepositoryContract $salesPriceRepository)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->salesPriceRepository = $salesPriceRepository;
    }
    
     public function home(Twig $twig) : string
     {
         return $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', null);
     }

     public function loginPriceMonitor(Twig $twig) : string
     {
        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', null);
     }

     public function login(Request $request,Twig $twig,LibraryCallContract $libCall,ContractRepositoryContract $contractRepo)  
     {
        $credentials = $request->all();

        if(empty($credentials['email']) || empty($credentials['password'])) {
           
                $errorReponse = [
                   'Code' => '500',
                   'Message' => 'Email and password fields are required!'
                ];

                return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

        try {

            $reponseContracts = $this->sdkService->call("getLoginAndContracts", [
                'email' => $credentials['email'],
                'password' => $credentials['password']
            ]);
            
            //Handling errors when ocuurs in getLoggingAndContracts
            if($reponseContracts != null && is_array($reponseContracts) && isset($reponseContracts['Code']) && isset($reponseContracts['Message']))
            {
                $errorReponse = null;

                if($reponseContracts['Code'] == 401)
                    $errorReponse = [
                        'Code' => $reponseContracts['Code'],
                        'Message' => 'Invalid credentials. Failed to login to Pricemonitor account.'
                    ];

                return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);

            }  

            
          // if contracts get successfully save them to DB
            else if($reponseContracts != null) 
              $contractRepo->saveContracts($reponseContracts);   
            
            // $contractRepo->deleteAllContracts();

             echo  json_encode($contractRepo->getContracts()); 

        } catch(\Exception $ex) {

            $errorReponse = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
            ];

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

        // set price monitor credentials
         $this->sdkService->call("setUpPriceMonitorCredentials", [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ]);
 
            $salesPricesRepo = pluginApp(SalesPriceRepositoryContract::class);

            $authHelper = pluginApp(AuthHelper::class);

            $salesPrices = null;

            $salesPrices = $authHelper->processUnguarded(
                function () use ($salesPricesRepo, $salesPrices) {
                
                    return $salesPricesRepo->all();
                }
            );

             $resultSalesPrices = $salesPrices->getResult();

             $salesPricesEnglish = array();
             
             foreach($resultSalesPrices as $prices)
             {  
                 foreach($prices['names'] as $key => $namePrice)
                 {
                    if($namePrice['lang'] != "en")
                        unset($prices['names'][$key]);

                       if($prices['names'][$key] != null) 
                            $salesPricesEnglish[] = $prices['names'][$key];
                 }                  
              }

        $templateData = array("contracts" => $reponseContracts,
                            "salesPrices" => $salesPricesEnglish);

       return  $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', $templateData);     
     }

     /**
      * @param Request                    $request
      * @param ContractRepositoryContract $contractRepo
      * @return string
      */
      public function updateContractInfo(Request $request, ContractRepositoryContract $contractRepo): string
      {

        echo json_encode($request->all());

         // $updateContractInfo = $contractRepo->updateContract($request->all());

          echo json_encode($updateContractInfo);

         // return json_encode($updateContractInfo);
      }
 }