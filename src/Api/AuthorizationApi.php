<?php
namespace PriceMonitorPlentyIntegration\Api;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Modules\Frontend\Contracts\Checkout;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;

class AuthorizationApi
{
    private $configInfoRepo;

    public function __construct(ConfigInfoRepository $configInfoRepo)
    {
        $this->configInfoRepo = $configInfoRepo;
    }

    public function checkToken(Request $request) 
    {
        $jwt = $request->header('Authorization', null);
        $access_token = "";

        if ($jwt != null ) {
            $access_token = explode(" ",$jwt)[1];
        }
        
        $savedToken =  $this->configInfoRepo->getConfig('access_token');
        $token = $savedToken !== null ? $savedToken->value : "";

        if ($access_token === null || $access_token != $token) {
            return false;
        }
        
        return true;
    }
}