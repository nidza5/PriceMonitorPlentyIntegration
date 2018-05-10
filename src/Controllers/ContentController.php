<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 

 use Plenty\Plugin\Controller;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;

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

     public function login(Request $request,Twig $twig)  
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

            $proxy = Proxy::createFor($credentials['email'],$credentials['password']);        
            $contracts = $proxy->getContracts();

        } catch(\Exception $ex) {

            echo "u exception kodu";

            return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', $response);
        }

        return $twig->render('PriceMonitorPlentyIntegration::content.loginpricemonitor', $contracts);     
     }
 }