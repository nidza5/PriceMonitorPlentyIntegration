<?php
 
namespace PriceMonitorPlentyIntegration\Providers;
 
use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
 
/**
 * Class ToDoRouteServiceProvider
 * @package PriceMonitorPlentyIntegration\Providers
 */
class PriceIntegrationRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @param Router $router
     */
    public function map(Router $router)
    {
        $router->get('integrationhome', 'PriceMonitorPlentyIntegration\Controllers\ContentController@home');
        $router->get('loginPriceMonitor', 'PriceMonitorPlentyIntegration\Controllers\ContentController@loginPriceMonitor');
        $router->post('login', 'PriceMonitorPlentyIntegration\Controllers\ContentController@login');
        $router->post('updateContractInfo', 'PriceMonitorPlentyIntegration\Controllers\ContentController@updateContractInfo');
        $router->get('getFilters', 'PriceMonitorPlentyIntegration\Controllers\FilterController@getFilters');
        $router->get('saveFilter', 'PriceMonitorPlentyIntegration\Controllers\FilterController@saveFilter');
        $router->get('getAttributes', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributes');
        $router->get('getAttributeValueByAttrId', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributeValueByAttrId');
    } 
}