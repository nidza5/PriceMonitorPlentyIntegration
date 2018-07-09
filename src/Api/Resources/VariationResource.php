<?php //strict

namespace PriceMonitorPlentyIntegration\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use PriceMonitorPlentyIntegration\Services\ProductFilterService;

/**
 * Class VariationResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class VariationResource extends ApiResource
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
        $itemService = pluginApp(ProductFilterService::class);
    
        $allVariations = $itemService->getAllVariations();

		return $this->response->create($allVariations, ResponseCode::OK);
	}

    /**
     * Create an order
     * @return Response
     */
	public function store():Response
	{
	
	}
}
