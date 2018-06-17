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
 use PriceMonitorPlentyIntegration\Constants\FilterType;
 use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
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

         /**
         *
         * @var ProductFilterRepository
         */
        private $productFilterRepo;


    public function __construct(ConfigRepository $config, PriceMonitorSdkService $sdkService,ProductFilterRepositoryContract $productFilterRepo)
    {
        $this->config = $config;
        $this->sdkService = $sdkService;       
        $this->productFilterRepo = $productFilterRepo;      
    }
    

      public function saveFilter(Request $request,ProductFilterRepositoryContract $productFilterRepo) : string
      {

        //  $productFilterRepo->deleteAllProductFilter();
          $requestData = $request->all();
       
         $filterForSave =  $this->sdkService->call("saveFilter", [
            'filterData' => $requestData['filters'],
            'filterType' => $requestData['type'],
            'priceMonitorId' => $requestData['pricemonitorId'],
            'productFilterRepositoryParam' => $productFilterRepo
        ]);

        echo "filters";
        echo json_encode( $requestData['filters']);
            
        if($filterForSave['contractId'] == null || $filterForSave['filterType'] == null || $filterForSave['filter'] == null)  
            throw new \Exception("some parameters of product filter are null");
            
        $resultFilter = $productFilterRepo->saveProductFilter($filterForSave);
        
        return json_encode($filterForSave['filter']);
      }

      public function getFilters(Request $request,ProductFilterRepositoryContract $productFilterRepo) :string 
      {
            // $productFilterRepo->deleteAllProductFilter();
            $requestData = $request->all();
            $priceMonitorId = 0;

            if($requestData == null)
                return;

            $priceMonitorId = $requestData['priceMonitorId'];
            $filterType = $requestData['filterType'];

            if($priceMonitorId == null || $filterType == null)
                throw new \Exception("Price monitor id or filter type is null");

            $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,$filterType);

            $filters = $this->sdkService->call("getFilterByTypeAndPriceMonitorId", [
                'filterType' => $filterType,
                'priceMonitorId' => $priceMonitorId,
                'productFilterRepo' => $filter
            ]);    

          return json_encode($filters);  

          //return "OK";
      }
      
 }