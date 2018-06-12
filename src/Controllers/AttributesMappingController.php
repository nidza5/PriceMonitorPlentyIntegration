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


 /**
  * Class AttributesMappingController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class AttributesMappingController extends Controller
 {
     use Loggable;
   

    public function getMappedAttributes(Request $request) :string 
    {
         return "OK";   
    }

    public function saveAttributesMapping(Request $request)
    {
        return "OK"; 
    }
 }