<?php

namespace PriceMonitorPlentyIntegration\Controllers;
 
 use Plenty\Plugin\Controller;
 use Plenty\Plugin\ConfigRepository;
 use Plenty\Plugin\Http\Request;
 use Plenty\Plugin\Templates\Twig;
 use Plenty\Plugin\Log\Loggable;
 use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
 use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;
 use Plenty\Modules\Authorization\Services\AuthHelper;
 use Plenty\Repositories\Models;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeRepositoryContract;
 use Plenty\Modules\Item\Property\Contracts\PropertyRepositoryContract;
 use Plenty\Modules\Item\Attribute\Contracts\AttributeValueRepositoryContract;
 
 /**
  * Class FilterController
  * @package PriceMonitorPlentyIntegration\Controllers
  */
 class FilterController extends Controller
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

    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;      
    }    

      public function saveFilter(Request $request) : string
      {
          $requestData = $request->all();

          $filterSave = $this->sdkService->call("filterSaveMiddleware", [
             'filters' => $requestData['filters'],
             'type' => $requestData['type'],
             'priceMonitorId' => $requestData['pricemonitorId']
          ]);

        return  json_encode($filterSave);
        
      }

      public function getFilters(Request $request) :string 
      {    
            $requestData = $request->all();

            if ($requestData == null) {
                throw new \Exception("Request data are null!");
            }
            
            $priceMonitorId = $requestData['priceMonitorId'];
            $filterType = $requestData['filterType'];

            if ($priceMonitorId == null || $filterType == null) {
                throw new \Exception("Price monitor id or filter type is null");
            }

            $filters =  $this->sdkService->call("getFilterFromMiddleware", [
                'filterType' =>  $filterType,
                'priceMonitorId' =>  $priceMonitorId
            ]);

           return json_encode($filters);  
      }

      public function filterPreview(Request $request) {

        $requestData = $request->all();

        if ($requestData == null) {
            throw new \Exception("RequestData is null");
        }

        $priceMonitorId = $requestData['pricemonitorId'];
        $filterType = $requestData['type'];

        if ($priceMonitorId == null || $filterType == null) {
            throw new \Exception("Price monitor id or filter type is null");
        }

        $filteredVariation =  $this->sdkService->call("previewFromMiddleware", [
            'filterType' => $filterType,
            'priceMonitorId' => $priceMonitorId           
        ]);

        return json_encode($filteredVariation);        
      }      
 }