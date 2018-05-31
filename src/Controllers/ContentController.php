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
         * @var AttributeRepositoryContract
         */
        private $attributeRepository;


    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,SalesPriceRepositoryContract $salesPriceRepository,ProductFilterRepositoryContract $productFilterRepo,AttributeRepositoryContract $attributeRepository)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->salesPriceRepository = $salesPriceRepository;        
        $this->productFilterRepo = $productFilterRepo;   
        $this->attributeRepository = $attributeRepository;     
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
         $this->sdkService->call("setUpPriceMonitorCredentials", [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ]);
 
          //  echo json_encode($stores);

          $attributesRepo = pluginApp(AttributeRepositoryContract::class);

          $authHelperAttr = pluginApp(AuthHelper::class);

          $attributes = null;

          $attributes = $authHelperAttr->processUnguarded(
            function () use ($attributesRepo, $attributes) {
            
                return $attributesRepo->all();
            }
        );

          echo json_encode($attributes);

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

      public function getFilters(Request $request) :string 
      {
            $requestData = $request->all();

            $priceMonitorId = 0;

            if($requestData != null)
                $priceMonitorId = $requestData['priceMonitorId'];

                $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,FilterType::EXPORT_PRODUCTS);

                $filters = $this->sdkService->call("getFilterByTypeAndPriceMonitorId", [
                    'filterType' => FilterType::EXPORT_PRODUCTS,
                    'priceMonitorId' => $priceMonitorId,
                    'productFilterRepo' => $filter
                ]);    

          return json_encode($filters);  

          //return "OK";
      }
 }