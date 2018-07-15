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
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use Plenty\Modules\Item\VariationSalesPrice\Contracts\VariationSalesPriceRepositoryContract;
use Plenty\Modules\Authorization\Services\AuthHelper;
use PriceMonitorPlentyIntegration\Constants\TransactionStatus;

/**
 * Class PriceResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class PriceResource extends ApiResource
{
    /**
     *
     * @var ContractRepositoryContract
     */
    private $contractRepo;

    /**
     * PriceResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
	public function __construct(Request $request,ApiResponse $response,ContractRepositoryContract $contractRepo)
	{
        parent::__construct($request, $response);
        $this->contractRepo = $contractRepo;
	}

	public function updatePrices():Response
	{
       $priceList =  $this->request->get('priceList', '');
       $pricemonitorContractId =  $this->request->get('pricemonitorContractId', '');

        if($priceList !== null)
            $priceList = json_decode($priceList,true);

       /** @var CurrencyExchangeRepository $currencyService */
       $currencyService = pluginApp(CurrencyExchangeRepositoryContract::class);
       $systemCurrency = $currencyService->getDefaultCurrency();

       foreach($priceList as $price) {
            if($price['currency'] ==  $systemCurrency) {
               
                if(empty($price['identifier']))
                    continue;

                $itemService = pluginApp(ProductFilterService::class);
                $originalVariation = $itemService->getVariationById($price['identifier']);

                $variation = null;
                if(!empty($originalVariation)) {
                    $variation = $originalVariation[0];
                    $variationSalesPrices = $variation["variationSalesPrices"];
                    $contractInformation =  $this->contractRepo->getContractByPriceMonitorId($contractId);
                    $savedSalesPriceInContract = $contractInformation->salesPricesImport;
                   
                    //sales price which related to variation
                    $salesPriceRelatedToVariation = $this->getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);
                
                    //sales price which not related to variation
                    $salesPricesNotRelatedToVariation = $this->getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);

                    try{
                        //update sales price that related to variation
                        $this->updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$price['identifier'],$price['recommendedPrice']);
                    } catch(\Exception $ex)
                    {
                        $failedItems[] = array(
                            'productId' => $price['identifier'],
                            'name' => isset($price['name']) ? $price['name'] : '',
                            'errors' => array('Unable to update product price.'),
                            'status' => TransactionHistoryStatus::FAILED
                        );
                    }                    
                    
                    if($contractInformation->isInsertSalesPrice && $salesPricesNotRelatedToVariation != null ) {
                        
                        try {
                            insertSalesPricesNotRelatedToVariation($salesPricesNotRelatedToVariation,$price['identifier'],$price['recommendedPrice']);
                        } catch(\Exception $ex) {
                            $failedItems[] = array(
                                'productId' => $price['identifier'],
                                'name' => isset($price['name']) ? $price['name'] : '',
                                'errors' => array('Unable to insert product price.'),
                                'status' => TransactionHistoryStatus::FAILED
                            );
                        }
                        
                    }
                    else if(!$contractInformation->isInsertSalesPrice && $salesPricesNotRelatedToVariation != null) {
                        // insert  to transaction history, transactionDetails
                        $failedItems [] = [
                            "productId" => $price['identifier'],   
                            "name" => isset($price['name']) ? $price['name'] : '',
                            "errors" => array("Sales prices which is selected to account info tab not related to variation and field is insert salesPrice selected as NO!"),
                            "status" => TransactionStatus::FAILED
                        ];                        
                    }
                }        
            } else {
                // enter to transactionHistory prices that don't have same currency 
                $failedItems [] = [
                    "productId" => $price['identifier'],   
                    "name" => isset($price['name']) ? $price['name'] : '',
                    "errors" => array("Sales prices which is selected to account info tab not related to variation and field is insert salesPrice selected as NO!"),
                    "status" => TransactionStatus::FAILED
                ];                
            }
        }

		return $this->response->create($failedItems, ResponseCode::OK);
    }
    
    private function insertSalesPricesNotRelatedToVariation($salesPricesNotRelatedToVariation,$variationId,$recommendedPrice)
    {
            foreach($salesPricesNotRelatedToVariation as $notRelatedPrice) {
                $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

                $authHelper = pluginApp(AuthHelper::class);
        
                $insertedSalesPrice = null;
    
                $dataForInsert = ["variationId" => $variationId,
                                  "salesPriceId" => $notRelatedPrice,
                                  "recommendedPrice" => $recommendedPrice];
        
                $insertedSalesPrice = $authHelper->processUnguarded(
                    function () use ($repositoryVariationSalesPrices, $insertedSalesPrice) {
                        return $repositoryVariationSalesPrices->create($dataForInsert);
                    }
                );
            }
    }

    private function updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$variationId,$recommendedPrice)
    {
        foreach($salesPriceRelatedToVariation as $relatedSalesPrice) {
            
            $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

            $authHelper = pluginApp(AuthHelper::class);
    
            $updatedSalesPrice = null;

            $dataForUpdate = ["variationId" => $variationId,
                              "salesPriceId" => $relatedSalesPrice,
                              "recommendedPrice" => $recommendedPrice];
    
            $updatedSalesPrice = $authHelper->processUnguarded(
                function () use ($repositoryVariationSalesPrices, $updatedSalesPrice) {
                    return $repositoryVariationSalesPrices->update($dataForUpdate, $relatedSalesPrice, $variationId);
                }
            );
        }
    }

    private function getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) 
    {
        $matchPrices = [];
        foreach($variationSalesPrices as $variationPrice) {
            if( !in_array($variationPrice["salesPriceId"], $savedSalesPriceInContract))
                $matchPrices[] = $variationPrice["salesPriceId"];
        }

        return $matchPrices;
    }

    private function getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) {

        $matchPrices = [];
        foreach($variationSalesPrices as $variationPrice) {
            if( in_array($variationPrice["salesPriceId"], $savedSalesPriceInContract))
                $matchPrices[] = $variationPrice["salesPriceId"];
        }

        return $matchPrices;
    }
}
