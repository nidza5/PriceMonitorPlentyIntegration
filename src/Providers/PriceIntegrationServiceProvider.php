<?php

namespace PriceMonitorPlentyIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Log\Services\ReferenceContainer;
use Plenty\Log\Exceptions\ReferenceTypeException;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;
use PriceMonitorPlentyIntegration\Helper\StringUtils;
use Plenty\Modules\Cron\Services\CronContainer;
use PriceMonitorPlentyIntegration\Services\CronScheduleUpdate;
use PriceMonitorPlentyIntegration\Services\CronSyncRun;
use PriceMonitorPlentyIntegration\Services\CronRefreshStatus;

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
         $this->getApplication()->bind(ConfigRepositoryContract::class, ConfigInfoRepository::class);
     }

     public function boot(ReferenceContainer $referenceContainer, CronContainer $cronContainer)
     {
        try {
            $referenceContainer->add([ 'ContractId' => 'ContractId' ]);
            // $cronContainer->add(CronContainer::EVERY_FIFTEEN_MINUTES, CronScheduleUpdate::class);
            // $cronContainer->add(CronContainer::EVERY_FIFTEEN_MINUTES, CronSyncRun::class);
            // $cronContainer->add(CronContainer::EVERY_FIFTEEN_MINUTES, CronRefreshStatus::class);
        }
        catch(ReferenceTypeException $ex) {
        
        }
    }
 }