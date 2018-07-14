<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Cloud\Storage\Models\StorageObject;
use Plenty\Modules\Cron\Contracts\CronHandler;
use Plenty\Modules\Frontend\Factories\FrontendFactory;
use Plenty\Modules\Plugin\Storage\Contracts\StorageRepositoryContract;
use Plenty\Plugin\CachingRepository;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;
use PriceMonitorPlentyIntegration\Constants\FilterType;
use PriceMonitorPlentyIntegration\Services\PriceMonitorSdkService;


class CronRefreshStatus extends CronHandler
{
    use Loggable;

        /**
         *
         * @var PriceMonitorSdkService
         */
        private $sdkService;


    public function __construct(PriceMonitorSdkService $sdkService)
    {
        $this->sdkService = $sdkService;
    }

    public function handle()
    {
        $cronScheduleUpdate = $this->sdkService->call("cronRefreshStatusMiddleware", []);        
    }
}