<?php
 
namespace PriceMonitorPlentyIntegration\Providers;
 
use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;
/**
 * Class ToDoRouteServiceProvider
 * @package PriceMonitorPlentyIntegration\Providers
 */
class PriceIntegrationRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @param Router $router
     */
    public function map(Router $router,ApiRouter $api)
    {

        $api->version(['v1'], ['namespace' => 'PriceMonitorPlentyIntegration\Api\Resources'], function ($api)
		{
            $api->post('priceMonitor/filter', 'FilterResource@store');
			
		});

        $router->get('integrationhome', 'PriceMonitorPlentyIntegration\Controllers\ContentController@home');
        $router->get('loginPriceMonitor', 'PriceMonitorPlentyIntegration\Controllers\ContentController@loginPriceMonitor');
        $router->post('login', 'PriceMonitorPlentyIntegration\Controllers\ContentController@login');
        $router->post('updateContractInfo', 'PriceMonitorPlentyIntegration\Controllers\ContentController@updateContractInfo');
        $router->get('getFilters', 'PriceMonitorPlentyIntegration\Controllers\FilterController@getFilters');
        $router->post('saveFilter', 'PriceMonitorPlentyIntegration\Controllers\FilterController@saveFilter');
        $router->get('getAttributes', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributes');
        $router->get('getAttributeValueByAttrId', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributeValueByAttrId');
    } 
}