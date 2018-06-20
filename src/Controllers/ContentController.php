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

    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,SalesPriceRepositoryContract $salesPriceRepository,ProductFilterRepositoryContract $productFilterRepo,ScheduleRepositoryContract $scheduleRepository,ConfigRepositoryContract $configInfoRepo)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->salesPriceRepository = $salesPriceRepository;        
        $this->productFilterRepo = $productFilterRepo;   
        $this->scheduleRepository = $scheduleRepository;   
        $this->configInfoRepo = $configInfoRepo;
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
            ]);

            // echo "response contracts    ";

            // echo json_encode($reponseContracts);

            $attributeService = pluginApp(AttributeService::class);

            $finalResult = $attributeService->getAllTypeAttributes();

            $attributesIdName = array();

            foreach($finalResult as $key => $value) {

                echo $key;
                echo $value;               

            }

            echo "Attributes id name";
            echo json_encode($attributesIdName);
            
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

        $this->configInfoRepo->saveConfig('email',$credentials['email']);
        $this->configInfoRepo->saveConfig('password',$credentials['password']);


         $this->sdkService->call("setUpPriceMonitorCredentials", [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ]);

        $allSchedule = $this->scheduleRepository->getAllSchedule();

        // echo json_encode($allSchedule);
 
          //  echo json_encode($stores);

        //   $originalfilter = $this->productFilterRepo->getAllFilters();
        //   echo "filters";
        //   echo json_encode($originalfilter);

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

        // $itemService = pluginApp(ProductFilterService::class);

        // $finalResult = $itemService->getAllVariations();

        // echo "products";
        // echo json_encode($finalResult);
              

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