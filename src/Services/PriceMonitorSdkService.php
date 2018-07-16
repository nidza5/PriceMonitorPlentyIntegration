<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Application;
use Plenty\Modules\Authorization\Services\AuthHelper;

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
       // $parameters['access_token'] = $this->config->get('access_token');
       
        return $this->libCall->call('PriceMonitorPlentyIntegration::' . $method, $parameters);
    }


}


?>