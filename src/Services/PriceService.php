<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Item\VariationSalesPrice\Contracts\VariationSalesPriceRepositoryContract;

class PriceService 
{  
    public function insertSalesPricesNotRelatedToVariation($savedSalesPrices, $variationId, $recommendedPrice)
    {
        foreach ($savedSalesPrices as $savedPrice) {
            $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

            $authHelper = pluginApp(AuthHelper::class);
    
            $dataForInsert = ["variationId" => $variationId,
                                "salesPriceId" => $savedPrice,
                                "price" => $recommendedPrice];
            
             $authHelper->processUnguarded(
                function () use ($repositoryVariationSalesPrices,$dataForInsert) {
                    return $repositoryVariationSalesPrices->create($dataForInsert);
                }
            );
        }
    }

    public function updateSalesPricesRelatedToVariation($salesPriceRelatedToVariation,$variationId,$recommendedPrice)
    {
        $productPrices = [];

        foreach ($salesPriceRelatedToVariation as $relatedSalesPrice) {         
            try {              
                    $repositoryVariationSalesPrices = pluginApp(VariationSalesPriceRepositoryContract::class);       

                    $authHelper = pluginApp(AuthHelper::class);
            
                    $dataForUpdate = ["variationId" => $variationId,
                                    "salesPriceId" => $relatedSalesPrice,
                                    "price" => $recommendedPrice];              
                                    
                     $authHelper->processUnguarded(
                        function () use ($repositoryVariationSalesPrices,$dataForUpdate,$relatedSalesPrice,$variationId) {
                            return $repositoryVariationSalesPrices->update($dataForUpdate, $relatedSalesPrice, $variationId);
                        }
                    );

                    $productPrices[$variationId][] = array (
                        'apiPrice' =>$recommendedPrice,
                        'price' => $relatedSalesPrice
                    );

            } catch(\Exception $ex) {}
            
        }

        return $productPrices;
    }

    public function getSalesPricesNotRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) 
    {
        $matchPrices = [];
        foreach ($variationSalesPrices as $variationPrice) {
            foreach ($savedSalesPriceInContract as $savedPrice) {
                if ($variationPrice["salesPriceId"] != $savedPrice) {
                    $matchPrices[] = $variationPrice["salesPriceId"];
                }               
            }         
        }

        return $matchPrices;
    }

    public function getSalesPricesRelatedForVariation($savedSalesPriceInContract, $variationSalesPrices) {

        $matchPrices = [];
        foreach($variationSalesPrices as $variationPrice) {
            foreach($savedSalesPriceInContract as $savedPrice) {
                if($variationPrice["salesPriceId"] ==  $savedPrice) {
                    $matchPrices[] = $variationPrice["salesPriceId"];
                }                    
            }
        }

        return $matchPrices;
    }
}

?>