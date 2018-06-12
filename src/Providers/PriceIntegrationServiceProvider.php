<?php

namespace PriceMonitorPlentyIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Log\Services\ReferenceContainer;
use Plenty\Log\Exceptions\ReferenceTypeException;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;

/**
 * Class PriceIntegrationServiceProvider
 * @package PriceMonitorPlentyIntegration\Providers
 */

 class PriceIntegrationServiceProvider extends ServiceProvider
 {
     /**
      * Register the service provider.
      */
     public function register()
     {
         $this->getApplication()->register(PriceIntegrationRouteServiceProvider::class);
         $this->getApplication()->bind(ContractRepositoryContract::class, ContractRepository::class);
         $this->getApplication()->bind(ProductFilterRepositoryContract::class, ProductFilterRepository::class);
         $this->getApplication()->bind(AttributesMappingRepositoryContract::class, AttributeMappingRepository::class);
     }

     public function boot(ReferenceContainer $referenceContainer)
    {
        // Register reference types for logs.
        try
        {
            $referenceContainer->add([ 'ContractId' => 'ContractId' ]);
        }
        catch(ReferenceTypeException $ex)
        {
        }
    }
 }