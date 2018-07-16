<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Application;
use Plenty\Modules\Authorization\Services\AuthHelper;
use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;

class PriceMonitorSdkService
{

    const GATEWAY_BASE_PATH = 'http://5824540b.ngrok.io';

    /**
     *
     * @var LibraryCallContract
     */
    private $libCall;

    /**
     *
     * @var ConfigRepository
     */
    private $config;

      /**
     *
     * @var ConfigRepositoryContract
     */
    private $configInfoRepo;

    /**
     *
     * @param LibraryCallContract $libCall
     * @param ConfigRepository $config
     */
    public function __construct(LibraryCallContract $libCall, ConfigRepository $config,ConfigRepositoryContract $configInfoRepo)
    {
        $this->libCall = $libCall;
        $this->config = $config;
        $this->configInfoRepo = $configInfoRepo;
    }

    /**
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function call(string $method, array $parameters)
    {

        $application = pluginApp(Application::class);

        $authHelper = pluginApp(AuthHelper::class);

        $plentyId = null;

        $plentyId = $authHelper->processUnguarded(
            function () use ($application, $plentyId) {
            
                return $application->getPlentyId();
            }
        );

        
        $token =  $this->configInfoRepo->getConfig('access_token');

        $parameters['gatewayBasePath'] = self::GATEWAY_BASE_PATH;
        $parameters['tenantId'] = "plenty_". $plentyId;
        $parameters['accessToken'] = $token !== null ? $token->value : "";
       
        return $this->libCall->call('PriceMonitorPlentyIntegration::' . $method, $parameters);
    }


}


?>