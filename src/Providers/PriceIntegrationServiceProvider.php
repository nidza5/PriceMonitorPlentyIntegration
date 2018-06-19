<?php

namespace PriceMonitorPlentyIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Log\Services\ReferenceContainer;
use Plenty\Log\Exceptions\ReferenceTypeException;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;
use PriceMonitorPlentyIntegration\Contracts\AttributesMappingRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\AttributesMappingRepository;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use PriceMonitorPlentyIntegration\Contracts\PriceMonitorQueueRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\PriceMonitorQueueRepository;
use PriceMonitorPlentyIntegration\Contracts\RunnerTokenRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\RunnerTokenRepository;
use PriceMonitorPlentyIntegration\Contracts\TransactionHistoryRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\TransactionHistoryRepository;
use PriceMonitorPlentyIntegration\Contracts\TransactionDetailsRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\TransactionDetailsRepository;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;


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
         $this->getApplication()->bind(AttributesMappingRepositoryContract::class, AttributesMappingRepository::class);
         $this->getApplication()->bind(ScheduleRepositoryContract::class, ScheduleRepository::class);
         $this->getApplication()->bind(PriceMonitorQueueRepositoryContract::class, PriceMonitorQueueRepository::class);
         $this->getApplication()->bind(RunnerTokenRepositoryContract::class, RunnerTokenRepository::class);
         $this->getApplication()->bind(TransactionHistoryRepositoryContract::class, TransactionHistoryRepository::class);
         $this->getApplication()->bind(TransactionDetailsRepositoryContract::class, TransactionDetailsRepository::class);
         $this->getApplication()->bind(ConfigRepositoryContract::class, ConfigInfoRepository::class);
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