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
    public function saveAttributeMapping(array $data);

 
    /**
     *  AttributeMapping
     *
     * @return AttributeMapping[]
     */
    public function getAttributeMappingByPriceMonitorId($priceMonitorId): AttributeMapping;



}