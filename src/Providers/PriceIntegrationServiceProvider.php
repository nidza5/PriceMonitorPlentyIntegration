<?php

namespace PriceMonitorPlentyIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Log\Services\ReferenceContainer;
use Plenty\Log\Exceptions\ReferenceTypeException;

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