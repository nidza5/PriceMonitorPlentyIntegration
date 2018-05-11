<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\ConfigRepository;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;

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
     * PaymentController constructor.
     * @param ConfigRepository $config
     * @param PriceMonitorSdkService $sdkService
     */
    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;
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

        echo json_encode($credentials);
        
        if(empty($credentials['email']) || empty($credentials['password'])) {
            // to do return some message for user
                $response = [
                   'StatusCode' => '500',
                   'message' => 'Email and password are empty!'
                ];

                echo "Empty email and password!";
                return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', $response);
        }

        try {

            $contracts = $this->sdkService->call("getLoginAndContracts", [
                'email' => $credentials['email'],
                'password' => $credentials['password']
            ]);

        } catch(\Exception $ex) {

            echo "u exception kodu";

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', $response);
        }

        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', $contracts);     
     }
 }