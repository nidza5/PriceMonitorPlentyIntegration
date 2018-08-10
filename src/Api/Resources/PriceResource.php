<?php //strict

namespace PriceMonitorPlentyIntegration\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use PriceMonitorPlentyIntegration\Services\AttributeService;
use Plenty\Modules\Frontend\Contracts\CurrencyExchangeRepositoryContract;   
use PriceMonitorPlentyIntegration\Services\ProductFilterService;
use Plenty\Modules\Item\VariationSalesPrice\Contracts\VariationSalesPriceRepositoryContract;
use Plenty\Modules\Authorization\Services\AuthHelper;
use PriceMonitorPlentyIntegration\Constants\TransactionStatus;
use PriceMonitorPlentyIntegration\Services\PriceService;
use PriceMonitorPlentyIntegration\Api\AuthorizationApi;

/**
 * Class PriceResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class PriceResource extends ApiResource
{
     /**
     *
     * @var priceService
     */
    private $priceService;

    /**
     * PriceResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
	public function __construct(Request $request,ApiResponse $response,PriceService $priceService)
	{
        parent::__construct($request, $response);
        $this->priceService = $priceService;
	}

	public function updatePrices():Response
	{
        $authorizeApi = pluginApp(AuthorizationApi::class);

        $isValid = $authorizeApi->checkToken($this->request);

        if ($isValid == false) {
            return $this->response->create("Unauthorized request", ResponseCode::UNAUTHORIZED);
        }

       $returnResult = ['productPrices' => [], 'errorMessages' => []];
       $priceList =  $this->request->get('priceList', '');
       $pricemonitorContractId =  $this->request->get('pricemonitorContractId', '');
       $contract =  $this->request->get('contract', '');
       $contractInformation = null;

       if ($priceList !== null) {
           $priceList = json_decode($priceList, true);
       }        

       if ($contract !== null) {
           $contractInformation = json_decode($contract, true);
        }

       /** @var CurrencyExchangeRepository $currencyService */
       $currencyService = pluginApp(CurrencyExchangeRepositoryContract::class);
       $systemCurrency = $currencyService->getDefaultCurrency();

       foreach ($priceList as $price) {
            if ($price['currency'] == $systemCurrency) {               
                if (empty($price['identifier'])) {
                    continue;
                }

                $itemService = pluginApp(ProductFilterService::class);
                $originalVariation = $itemService->getVariationById($price['identifier']);

                $variation = null;
                if (!empty($originalVariation)) {
                    $variation = $originalVariation[0];
                    $variationSalesPrices = $variation["variationSalesPrices"];
                    $savedSalesPriceInContract = $contractInformation['salesPricesImport'];
                    
                    if ($savedSalesPriceInContract != null || $savedSalesPriceInContract != "") {
                        $savedSalesPriceInContract = explode(",",$savedSalesPriceInContract);
                    }                       

                    //sales price which related to variation
                    $salesPriceRelatedToVariation = $this->priceService->getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);

                    //sales price which not related to variation
                    $salesPricesNotRelatedToVariation = $this->priceService->getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);

                    try {
                        //update sales price that related to variation
                        $update = $this->priceService->updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$price['identifier'],$price['recommendedPrice']);

                        $returnResult['productPrices'][] = $update;

                } catch(\Exception $ex)
                    {
                        $failedItems[] = array(
                            'productId' => $price['identifier'],
                            'name' => isset($price['name']) ? $price['name'] : '',
                            'errors' => array('Unable to update product price.'),
                            'status' => TransactionStatus::FAILED
                        );

                        $responseError = [
                            'productId' => $price['identifier'],
                            'errors' => array('Unable to update product price.'),
                            'status' => TransactionStatus::FAILED
                        ];

                        $returnResult['errorMessages'][] = $responseError;
                    }                   
                    
                    if ($contractInformation['isInsertSalesPrice'] && $salesPriceRelatedToVariation == null) {
                        try {
                            $insert =  $this->priceService->insertSalesPricesNotRelatedToVariation($savedSalesPriceInContract,$price['identifier'],$price['recommendedPrice']);
                            return $this->response->create($insert, ResponseCode::OK);
                        } catch (\Exception $ex) {
                            $failedItems[] = array(
                                'productId' => $price['identifier'],
                                'name' => isset($price['name']) ? $price['name'] : '',
                                'errors' => array('Unable to insert product price.'),
                                'status' => TransactionStatus::FAILED
                            );

                            $responseError = [
                                'productId' => $price['identifier'],
                                'errors' => array('Unable to insert product price.'),
                                'status' => TransactionStatus::FAILED
                            ];
    
                            $returnResult['errorMessages'][] = $responseError;
                        }
                        
                    }
                    else if (!$contractInformation['isInsertSalesPrice'] && $salesPricesNotRelatedToVariation != null && $salesPriceRelatedToVariation == null) {
                        // insert  to transaction history, transactionDetails
                        $failedItems [] = [
                            "productId" => $price['identifier'],   
                            "name" => isset($price['name']) ? $price['name'] : '',
                            "errors" => array("Sales prices which is selected to account info tab not related to variation and field is insert salesPrice selected as NO!"),
                            "status" => TransactionStatus::FAILED
                        ];        
                        
                        $responseError = [
                            'productId' => $price['identifier'],
                            'errors' => array('Sales prices which is selected to account info tab not related to variation and field is insert salesPrice selected as NO!'),
                            'status' => TransactionStatus::FAILED
                        ];

                        $returnResult['errorMessages'][] = $responseError;
                    }
                }        
            } else {
                // enter to transactionHistory prices that don't have same currency 
                $failedItems [] = [
                    "productId" => $price['identifier'],   
                    "name" => isset($price['name']) ? $price['name'] : '',
                    "errors" => array("Price that returned don't have some currency as sales price!"),
                    "status" => TransactionStatus::FAILED
                ];                

                $responseError = [
                    'productId' => $price['identifier'],
                    'errors' => array('Price that returned dont have some currency as sales price!'),
                    'status' => TransactionStatus::FAILED
                ];

                $returnResult['errorMessages'][] = $responseError;                
            }
        }
        
		return $this->response->create($returnResult, ResponseCode::OK);
    }   
}
