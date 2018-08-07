<?php // strict

namespace PriceMonitorPlentyIntegration\Middlewares;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Modules\Frontend\Contracts\Checkout;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;

class Middleware extends \Plenty\Plugin\Middleware
{
    protected $configInfoRepo;

    protected $response;

    public function __construct(
        ConfigInfoRepository $configInfoRepo,
        Response $response)
    {
        $this->configInfoRepo = $configInfoRepo;
        $this->response       = $response;
    }

    public function before(Request $request )
    {
                
    }

    public function after(Request $request, Response $response):Response
    {
        return $response;
    }

}