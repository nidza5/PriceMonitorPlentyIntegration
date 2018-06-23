<?php

use Patagona\Pricemonitor\Core\Infrastructure\Logger;
use Patagona\Pricemonitor\Core\Interfaces\PriceService as PriceServiceInterface;
use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryStatus;

class PriceService implements PriceServiceInterface
{
    /**
     * Patagona_Pricemonitor_Service_Core_MapperService constructor.
     */
    public function __construct()
    {

    }

    /**
     * Updates integration system prices based on a Pricemonitor price list
     *
     * @param string $pricemonitorContractId Pricemonitor ID
     * @param array $priceList List of PM recommended prices. Each price in a list has fields:
     *  - identifier: Product id in shop (same value sent via productId when exporting products to PM)
     *  - recommendedPrice: New PM recommended price for a product
     *  - gtin: Product gtin
     *  - currency: Price currency in a ISO 4217 code (3 letter currency code, EUR, USD, BGP...)
     *
     * @return array List of errors if any. Each error in a list will be in format:
     * ['productId' => identifier, errors => ['error message1', 'error message 2', ...]]
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function updatePrices($pricemonitorContractId, $priceList)
    {
        
    }
}