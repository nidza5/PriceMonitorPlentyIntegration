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
          $requestData = $request->all();

          
         // return json_encode($productFilterRepo);

         $filterForSave =  $this->sdkService->call("saveFilter", [
            'filterData' => $requestData['filters'],
            'filterType' => $requestData['type'],
            'priceMonitorId' => $requestData['pricemonitorId'],
            'productFilterRepositoryParam' => $productFilterRepo
        ]);
            
        
        
         return json_encode($filterForSave);

        // return $filterForSave;

        // $productFilter = $productFilterRepo->getFilterByContractIdAndType($requestData['pricemonitorId'],FilterType::EXPORT_PRODUCTS);
        // return json_encode($productFilter);

      }

      public function getFilters(Request $request,ProductFilterRepositoryContract $productFilterRepo) :string 
      {
            $requestData = $request->all();

            $priceMonitorId = 0;

            if($requestData != null)
                $priceMonitorId = $requestData['priceMonitorId'];

               // $filter = $this->productFilterRepo->getFilterByContractIdAndType($priceMonitorId,FilterType::EXPORT_PRODUCTS);

                $filters = $this->sdkService->call("getFilterByTypeAndPriceMonitorId", [
                    'filterType' => FilterType::EXPORT_PRODUCTS,
                    'priceMonitorId' => $priceMonitorId,
                    'productFilterRepo' => $productFilterRepo
                ]);    

          return json_encode($filters);  

          //return "OK";
      }
 }