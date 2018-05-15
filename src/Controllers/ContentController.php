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
         * @var configService
         */

        private $configService;


    /**
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
        $this->configService = ServiceRegister::getConfigService();
    }
    
     public function home(Twig $twig) : string
     {
         return $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', null);
     }

     public function loginPriceMonitor(Twig $twig) : string
     {
        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', null);
     }

     public function login(Request $request,Twig $twig,LibraryCallContract $libCall)  
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

        } catch(\Exception $ex) {

            $errorReponse = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
            ];

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', ['errorReponse' => $errorReponse ]);
        }

        $this->configService>setCredentials($credentials['email'],$credentials['password']);

       return  $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', $reponseContracts);     
     }
 }