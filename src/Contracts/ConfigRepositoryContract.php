<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;

/**
 * Class ConfigRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface ConfigRepositoryContract
{
    /**
     * Save attributeMappings
     *
     * @param array $data
     * @return void
     */
    public function saveConfig($key, $value);

    public function getConfig($key);
}