<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Plugin\Libs\Contracts\LibraryCallContract;
use Plenty\Plugin\ConfigRepository;

class PriceMonitorSdkService
{

  //  const GATEWAY_BASE_PATH = 'http://47343d5b.ngrok.io';

  const GATEWAY_BASE_PATH = 'http://64bada2e.ngrok.io';
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
        $parameters['gatewayBasePath'] = self::GATEWAY_BASE_PATH;
        return $this->libCall->call('PriceMonitorPlentyIntegration::' . $method, $parameters);
    }


}


?>