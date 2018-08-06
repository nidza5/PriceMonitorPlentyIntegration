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

        // $jwt = $request->header('Authorization', null);
        // $access_token = "";

        // if ($jwt != null ) {
        //     $access_token = explode(" ",$jwt)[1];
        // }

        // $savedToken =  $this->configInfoRepo->getConfig('access_token');
        // $token = $savedToken !== null ? $savedToken->value : "";

        // if ($access_token === null || $access_token != $token) {
        //     return $this->response->make("Unauthorized request", 401);
        // }
    }

}