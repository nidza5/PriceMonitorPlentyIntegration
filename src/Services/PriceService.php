<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Item\VariationSalesPrice\Contracts\VariationSalesPriceRepositoryContract;
use PriceMonitorPlentyIntegration\Constants\TransactionStatus;
use PriceMonitorPlentyIntegration\Services\AttributeService;


class PriceService 
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
	public function __construct(ContractRepositoryContract $contractRepo)
	{
        $this->contractRepo = $contractRepo;
	}
  
    public function insertSalesPricesNotRelatedToVariation($savedSalesPrices,$variationId,$recommendedPrice)
    {
            return $savedSalesPrices;

            foreach($savedSalesPrices as $savedPrice) {
                $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

                $authHelper = pluginApp(AuthHelper::class);
        
                $insertedSalesPrice = null;
    
                $dataForInsert = ["variationId" => $variationId,
                                  "salesPriceId" => $savedPrice,
                                  "price" => $recommendedPrice];

                return  $dataForInsert;                  
        
                $insertedSalesPrice = $authHelper->processUnguarded(
                    function () use ($repositoryVariationSalesPrices, $insertedSalesPrice,$dataForInsert) {
                        return $repositoryVariationSalesPrices->create($dataForInsert);
                    }
                );
            }
    }

    public function updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$variationId,$recommendedPrice)
    {
        foreach($salesPriceRelatedToVariation as $relatedSalesPrice) {
            
            $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

            $authHelper = pluginApp(AuthHelper::class);
    
            $updatedSalesPrice = null;

            $dataForUpdate = ["variationId" => $variationId,
                              "salesPriceId" => $relatedSalesPrice,
                              "price" => $recommendedPrice];
            
    
            $updatedSalesPrice = $authHelper->processUnguarded(
                function () use ($repositoryVariationSalesPrices, $updatedSalesPrice,$dataForUpdate,$relatedSalesPrice,$variationId) {
                    return $repositoryVariationSalesPrices->update($dataForUpdate, $relatedSalesPrice, $variationId);
                }
            );
        }
    }

    public function getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) 
    {
        $matchPrices = [];
        foreach($variationSalesPrices as $variationPrice) {
            if( $variationPrice["salesPriceId"] != $savedSalesPriceInContract)
                $matchPrices[] = $variationPrice["salesPriceId"];
        }

        return $matchPrices;
    }

    public function getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) {

        $matchPrices = [];
        foreach($variationSalesPrices as $variationPrice) {
            if( $variationPrice["salesPriceId"] ==  $savedSalesPriceInContract)
                $matchPrices[] = $variationPrice["salesPriceId"];
        }

        return $matchPrices;
    }
}

?>