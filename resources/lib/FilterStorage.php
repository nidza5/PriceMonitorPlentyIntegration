<?php

require_once __DIR__ . '/PriceMonitorHttpClient.php';


use Patagona\Pricemonitor\Core\Interfaces\FilterStorage as FilterStorageInterface;
use Patagona\Pricemonitor\Core\Infrastructure\Logger;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
use PriceMonitorPlentyIntegration\Models\ProductFilter;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;


class FilterStorage implements FilterStorageInterface
{
    /**
    *
    * @var ProductFilterRepository
    */
    private $productFilter;

    private $productRepo;

    public function __construct($productRepo)
    {
        $this->productRepo = $productRepo;
    }

    /**
     * Saves serialized filter.
     *
     * @param string $contractId Pricemonitor contract ID.
     * @param string $type Possible values export_products and import_prices.
     * @param string $filter Serialized Filter object.
     *
     * @return void
     */
     public function saveFilter($contractIds, $type, $filter)
     { 

        //  $dataTransfer = [
        //         "ContractId" => $contractIds,
        //          "type" => $type,
        //          "filter" => $filter
        //  ];

        // $client = new PriceMonitorHttpClient();
        // $savedData =  $client->request("POST", "https://023c892989219d0ada8822ece320bfdf1a4deb5a.plentymarkets-cloud-de.com/priceMonitor/filter",[], json_encode($dataTransfer));
        // return $savedData;

        // $filterOriginals = pluginApp(ProductFilter::class);
        // $this->dataBase->save($filterOriginals); 

        // $f = $this->productRepo->getFilterByContractIdAndType($contractIds,"export_products");
        // return $f;
     }
 
     /**
      * Gets serialized filter from the DB.
      *
      * @param string $contractId Pricemonitor contract ID.
      * @param string $type Possible values export_products and import_prices.
      *
      * @return string|null
      */
     public function getFilter($idContract, $typeFilter)
     {
           return ($this->productRepo !== null && $this->productRepo['serializedFilter'] != null) ? $this->productRepo['serializedFilter']['serializedFilter'] : null;
     }
}

?>