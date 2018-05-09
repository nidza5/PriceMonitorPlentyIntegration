<?php
 
 namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;
 
 /**
  * Class ContentController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class ContentController extends Controller
 {
     use Loggable;
 
     public function home(Twig $twig) : string
     {
         return $twig->render('PriceMonitorPlentyIntegration::content.priceIntegration', null);
     }

     public function loginPriceMonitor(Twig $twig) : string
     {
        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', null);
     }

     public function login(Request $request)  
     {
        $credentials = $request->all();
        
        if(empty($credentials['email']) || empty($credentials['password'])) {
            // to do return some message for user
                $response = [
                   'StatusCode' => '500',
                   'message' => 'Email and password are incorect!'
                ];

                return json_encode($response);
        }

        $proxy = Proxy::createFor($credentials['email'],$credentials['password']);
        
        try {

           $contracts = $proxy->getContracts();
            
            return json_encode($contracts);

        } catch(Exception $ex) {
           $error = $ex -> getMessages();
           return json_encode($error);
        }
     }
 }