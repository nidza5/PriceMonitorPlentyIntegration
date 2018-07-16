<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Application;

class PriceMonitorSdkService
{

    const GATEWAY_BASE_PATH = 'http://ced91e36.ngrok.io';

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
     * @param LibraryCallContract $libCall
     * @param ConfigRepository $config
     */
    public function __construct(LibraryCallContract $libCall, ConfigRepository $config)
    {
        $this->libCall = $libCall;
        $this->config = $config;
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

        $parameters['gatewayBasePath'] = self::GATEWAY_BASE_PATH;
        $parameters['tenantId'] = "plenty_". $plentyId;

        return $this->libCall->call('PriceMonitorPlentyIntegration::' . $method, $parameters);
    }


}


?>