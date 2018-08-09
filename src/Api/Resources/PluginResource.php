<?php //strict

namespace PriceMonitorPlentyIntegration\Api\Resources;

use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use PriceMonitorPlentyIntegration\Api\ApiResource;
use PriceMonitorPlentyIntegration\Api\ApiResponse;
use PriceMonitorPlentyIntegration\Api\ResponseCode;
use PriceMonitorPlentyIntegration\Services\AttributeService;
use PriceMonitorPlentyIntegration\Api\AuthorizationApi;
use Plenty\Modules\Plugin\Contracts\PluginRepositoryContract;
use Plenty\Modules\Authorization\Services\AuthHelper;

/**
 * Class PluginResource
 * @package PriceMonitorPlentyIntegration\Api\Resources
 */
class PluginResource extends ApiResource
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
     * List the plugins
     * @return Response
     */
	public function index():Response
	{
        // $authorizeApi = pluginApp(AuthorizationApi::class);

        // $isValid = $authorizeApi->checkToken($this->request);

        // if ($isValid == false) {
        //     return $this->response->create("Unauthorized request", ResponseCode::UNAUTHORIZED);
        // }

        $pluginsRepo = pluginApp(PluginRepositoryContract::class);

        $authHelper = pluginApp(AuthHelper::class);

        $plugins = null;

        $plugins = $authHelper->processUnguarded(
          function () use ($pluginsRepo, $plugins) {          
              return $pluginsRepo->searchPlugins(['name' => 'PriceMonitorPlentyIntegration']);
            }
        );
        
        $resultPlugins = $plugins->getResult();

		return $this->response->create($resultPlugins, ResponseCode::OK);
	}
}
