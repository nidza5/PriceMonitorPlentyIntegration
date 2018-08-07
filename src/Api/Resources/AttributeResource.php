<?php //strict

namespace PriceMonitorPlentyIntegration\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use PriceMonitorPlentyIntegration\Services\AttributeService;
use PriceMonitorPlentyIntegration\Api\AuthorizationApi;

/**
 * Class VariationResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class AttributeResource extends ApiResource
{
    /**
     * OrderResource constructor.
     * @param Request $request
     * @param ApiResponse $response
     */
	public function __construct(
		Request $request,
		ApiResponse $response)
	{
		parent::__construct($request, $response);
	}

    /**
     * List the variations of the customer
     * @return Response
     */
	public function index():Response
	{
        $authorizeApi = pluginApp(AuthorizationApi::class);

        $isValid = $authorizeApi->checkToken($this->request);

        if ($isValid == false) {
            return $this->response->error(401, 'Unauthorized request');
        }

        $attributeService = pluginApp(AttributeService::class);
    
        $attributes = $attributeService->getAllTypeAttributes();

        $attributesIdName = array();
    
        foreach ($attributes as $key => $value) {
            foreach ($value as $v => $l) {
                $attributesIdName[$v] = explode("-",$l)[0]; 
            }                               
        }

		return $this->response->create($attributesIdName, ResponseCode::OK);
	}
}
