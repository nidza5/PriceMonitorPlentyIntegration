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
use PriceMonitorPlentyIntegration\Services\PriceService;

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
     *
     * @var priceService
     */
    private $priceService;

    /**
     * PriceResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
	public function __construct(Request $request,ApiResponse $response,ContractRepositoryContract $contractRepo,PriceService $priceService)
	{
        parent::__construct($request, $response);
        $this->contractRepo = $contractRepo;
        $this->priceService = $priceService;
	}

	public function updatePrices():Response
	{

        echo "u update prices";

       $priceList =  $this->request->get('priceList', '');
       $pricemonitorContractId =  $this->request->get('pricemonitorContractId', '');
       $contract =  $this->request->get('contract', '');
       $contractInformation = null;

       return array('priceList' => $priceList, "contract" => $contract, "pricemonitorContractId" =>  $pricemonitorContractId);

        if($priceList !== null)
            $priceList = json_decode($priceList,true);

         if($contract !== null)
            $contractInformation = json_decode($contract,true);


      

        echo "priceList";
        echo json_encode($priceList);
        
        echo "contractInformation";
        echo json_encode($contractInformation);

        echo "pricemonitorContractId";
        echo $pricemonitorContractId;

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
                    $savedSalesPriceInContract = $contractInformation['salesPricesImport'];
                   
                    //sales price which related to variation
                    $salesPriceRelatedToVariation = $this->priceService->getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);
                
                    //sales price which not related to variation
                    $salesPricesNotRelatedToVariation = $this->priceService->getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices);

                    try{
                        //update sales price that related to variation
                        $this->priceService->updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$price['identifier'],$price['recommendedPrice']);
                    } catch(\Exception $ex)
                    {
                        $failedItems[] = array(
                            'productId' => $price['identifier'],
                            'name' => isset($price['name']) ? $price['name'] : '',
                            'errors' => array('Unable to update product price.'),
                            'status' => TransactionStatus::FAILED
                        );
                    }                    
                    
                    if($contractInformation['isInsertSalesPrice'] && $salesPricesNotRelatedToVariation != null ) {
                        
                        try {
                            $this->priceService->insertSalesPricesNotRelatedToVariation($salesPricesNotRelatedToVariation,$price['identifier'],$price['recommendedPrice']);
                        } catch(\Exception $ex) {
                            $failedItems[] = array(
                                'productId' => $price['identifier'],
                                'name' => isset($price['name']) ? $price['name'] : '',
                                'errors' => array('Unable to insert product price.'),
                                'status' => TransactionStatus::FAILED
                            );
                        }
                        
                    }
                    else if(!$contractInformation['isInsertSalesPrice'] && $salesPricesNotRelatedToVariation != null) {
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
}
