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
 use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceRepositoryContract;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
 use Plenty\Modules\Market\Credentials\Contracts\CredentialsRepositoryContract;
 use PriceMonitorPlentyIntegration\Constants\FilterType;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
 use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use PriceMonitorPlentyIntegration\Services\AttributeService;

 //todo delete
 use PriceMonitorPlentyIntegration\Services\ProductFilterService;
 use PriceMonitorPlentyIntegration\Services\PaymentService;
 use PriceMonitorPlentyIntegration\Services\ConfigService;
 use Plenty\Modules\Frontend\Contracts\CurrencyExchangeRepositoryContract;
 use Plenty\Plugin\Http\Request as PluginRequest;
 use Plenty\Plugin\Application;
 use Plenty\Modules\Property\Contracts\PropertyAvailabilityRepositoryContract;

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
         *
         * @var WebstoreRepositoryContract
         */
         private $webStoreRepositoryContract;
        
        /**
         *
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;

        private $configService;

    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,SalesPriceRepositoryContract $salesPriceRepository,ConfigRepositoryContract $configInfoRepo,ConfigService $configService)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->salesPriceRepository = $salesPriceRepository;     
        $this->configInfoRepo = $configInfoRepo;
        $this->configService = $configService;
    }
    
     public function home(Twig $twig) : string
     {
         return $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', null);
     }

     public function loginPriceMonitor(Twig $twig) : string
     {
        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', null);
     }

     public function login(Request $request,Twig $twig,LibraryCallContract $libCall,ConfigRepositoryContract $configInfoRepo)  
     {
        $credentials = $request->all();

        if (empty($credentials['email']) || empty($credentials['password'])) {
           
                $errorReponse = [
                   'Code' => '500',
                   'Message' => 'Email and password fields are required!'
                ];

                return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

        try {

            $hostRepo = pluginApp(PluginRequest::class);

            $authHelper = pluginApp(AuthHelper::class);

            $host = null;

            $host = $authHelper->processUnguarded(
                function () use ($hostRepo, $host) {
                
                    return $hostRepo->getHttpHost();
                }
            );

            $resultLogin = $this->sdkService->call("getLoginAndContracts", [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'host' =>  $host             
            ]);

            //Handling errors when ocuurs in getLoggingAndContracts
            if (($resultLogin != null && is_array($resultLogin) && isset($resultLogin['Code']) && isset($resultLogin['Message'])) || ($resultLogin['error'] && $resultLogin['error_msg']))
            {
                $errorReponse = null;

                if ($resultLogin['Code'] == 401) {
                    $errorReponse = [
                        'Code' => $resultLogin['Code'],
                        'Message' => 'Invalid credentials. Failed to login to Pricemonitor account.'
                    ];
                }              

                return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
            }            
            
            $contractsFromMiddleware =  $resultLogin['contracts'];

            $accessToken = $resultLogin['access_token'];

            $dashboardInfo = $resultLogin['dashboardInfo'];

            $configInfoRepo->saveConfig('access_token',$accessToken);

        } catch(\Exception $ex) {

            $errorReponse = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
            ];

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

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
             
             foreach ($resultSalesPrices as $prices) {  
                 foreach ($prices['names'] as $key => $namePrice) {
                    if ($namePrice['lang'] != "en") {
                        unset($prices['names'][$key]);
                    }                        

                    if ($prices['names'][$key] != null) {
                        $salesPricesEnglish[] = $prices['names'][$key];
                    }                            
                 }                  
              }

        $contractsIds = array();

        $itemService = pluginApp(ProductFilterService::class);

        $templateData = array("contracts" => $contractsFromMiddleware,
                            "salesPrices" => $salesPricesEnglish,
                            'dashboardInfo' => $dashboardInfo);

       return  $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', $templateData);     
     }

     /**
      * @param Request $request
      * @return string
      */
      public function updateContractInfo(Request $request, Twig $twig): string
      {
        $requestData = $request->all();

        $updateContract = $this->sdkService->call("updateContractInfo", [
            'idContract' => $requestData['id'],
            'priceMonitorId' => $requestData['priceMonitorId'],
            'salesPriceImportIn' => $requestData['salesPricesImport'],
            'isInsertSalesPrice' => $requestData['isInsertSalesPrice']      
        ]);

        return json_encode($updateContract);
        
      }
 }