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
 use Plenty\Modules\System\Contracts\WebstoreRepositoryContract;
 use Plenty\Modules\Market\Credentials\Contracts\CredentialsRepositoryContract;
 use PriceMonitorPlentyIntegration\Constants\FilterType;
 use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
 use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
 use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
 use PriceMonitorPlentyIntegration\Services\AttributeService;

 //todo delete
 use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
 use PriceMonitorPlentyIntegration\Services\ProductFilterService;
 use PriceMonitorPlentyIntegration\Services\PaymentService;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorHttpClient;
 use PriceMonitorPlentyIntegration\Services\ConfigService;
 use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\PriceMonitorQueueRepository;


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
         * @var ProductFilterRepository
         */
        private $productFilterRepo;

           /**
         *
         * @var ScheduleRepositoryContract
         */
        private $scheduleRepository;
        
        /**
         *
         * @var ConfigRepositoryContract
         */
        private $configInfoRepo;

        private $httpClient;

        private $configService;

        /**
         *
         * @var PriceMonitorQueueRepositoryContract
         */
        private $queueRepo;

    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,SalesPriceRepositoryContract $salesPriceRepository,ProductFilterRepositoryContract $productFilterRepo,ScheduleRepositoryContract $scheduleRepository,ConfigRepositoryContract $configInfoRepo,PriceMonitorHttpClient $httpClient,ConfigService $configService,PriceMonitorQueueRepositoryContract $queueRepo)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->salesPriceRepository = $salesPriceRepository;        
        $this->productFilterRepo = $productFilterRepo;   
        $this->scheduleRepository = $scheduleRepository;   
        $this->configInfoRepo = $configInfoRepo;
        $this->httpClient = $httpClient;
        $this->configService = $configService;
        $this->queueRepo = $queueRepo;
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

        //   $contractRepo->deleteAllContracts();

        try {

            $reponseContracts = $this->sdkService->call("getLoginAndContracts", [
                'email' => $credentials['email'],
                'password' => $credentials['password']
                //  'httpClient' => $this->httpClient
            ]);

            // echo "response contracts    ";

            // echo json_encode($reponseContracts);

            
            //Handling errors when ocuurs in getLoggingAndContracts
            if(($reponseContracts != null && is_array($reponseContracts) && isset($reponseContracts['Code']) && isset($reponseContracts['Message'])) || ($reponseContracts['error'] && $reponseContracts['error_msg']))
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
            
            $originalContracts = $contractRepo->getContracts(); 

            // echo json_encode($originalContracts);

        } catch(\Exception $ex) {

            $errorReponse = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
            ];

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

        // set price monitor credentials

        // $this->configInfoRepo->saveConfig('email',$credentials['email']);
        // $this->configInfoRepo->saveConfig('password',$credentials['password']);

        // $this->queueRepo->deleteAllQueue();

        // $this
        //      ->getLogger('ContentController_login')
        //      ->info('PriceMonitorPlentyIntegration::migration.successMessage', ['email' => $credentials['email'], 'password' => $credentials['password'] ]);

        $webHookToken = $this->configInfoRepo->getConfig('webhook_token');
        $tokenForSend = $webHookToken->value;

        $setUpCredential= $this->sdkService->call("setUpPriceMonitorCredentials", [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'configService' => $this->configService
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

        $contractsIds = array();

        $itemService = pluginApp(ProductFilterService::class);

         $finalResult = $itemService->getAllVariations();
       // $finalResult = $itemService->getManufacturerById(2);
       //  $finalResult = $itemService->getItemWithPropertiesById(135);
     
        echo "products";
        echo json_encode($finalResult);
              
        // $prodServ = pluginApp(ProductFilterService::class);

        //  $categories =  $prodServ->getCategoryById(18);

        //  echo json_encode( $categories[0]["details"][0]["name"]);

        $templateData = array("contracts" => $originalContracts,
                            "salesPrices" => $salesPricesEnglish);

       return  $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', $templateData);     
     }

     /**
      * @param Request                    $request
      * @param ContractRepositoryContract $contractRepo
      * @return string
      */
      public function updateContractInfo(Request $request, Twig $twig,ContractRepositoryContract $contractRepo): string
      {
         $updateContractInfo = $contractRepo->updateContract($request->all());
        
         $contractInfo = null;

         if(($updateContractInfo != null) && ($updateContractInfo->id != 0) && ($updateContractInfo->id != null))
            $contractInfo = $contractRepo->getContractById($updateContractInfo->id);

         return json_encode($contractInfo); 
      }
 }