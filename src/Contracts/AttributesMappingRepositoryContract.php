<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\AttributeMapping;
 
/**
 * Class AttributesMappingRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface AttributesMappingRepositoryContract
{
    /**
     * Save attributeMappings
     *
     * @param array $data
     * @return void
     */
    public function saveAttributeMapping($contractId, $contractPricemonitorId,array $mappings);

 
    /**
     *  AttributeMapping
     *
     * @return AttributeMapping
     */
    public function getAttributeMappingByPriceMonitorId($priceMonitorId): AttributeMapping;


    public function deleteMappingsForContract($contractPriceMonitorId);

    public function getAttributeMappingCollectionByPriceMonitorId($priceMonitorId);

    public function getAllAttributeMappings();
}