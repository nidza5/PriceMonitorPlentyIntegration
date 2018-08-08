<?php //strict

namespace PriceMonitorPlentyIntegration\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use PriceMonitorPlentyIntegration\Services\ProductFilterService;
use PriceMonitorPlentyIntegration\Api\AuthorizationApi;

/**
 * Class ProductResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class ProductResource extends ApiResource
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
     * List of filtered products
     * @return Response
     */
	public function index():Response
	{
        $authorizeApi = pluginApp(AuthorizationApi::class);

        $isValid = $authorizeApi->checkToken($this->request);

        if ($isValid == false) {
            return $this->response->create("Unauthorized request", ResponseCode::UNAUTHORIZED);
        }

        $parentGroup =  $this->request->get('parentGroup', '');

        $groupOperator =  $this->request->get('groupOperator', '');

        if ($parentGroup !== null) {
            $parentGroup = json_decode($parentGroup,true);
        }
        
        $itemService = pluginApp(ProductFilterService::class);
    
        $filteredProducts = $itemService->addFilterByOperator($parentGroup,$groupOperator); 

		return $this->response->create($filteredProducts, ResponseCode::OK);
	}
}
