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

        // $api->version(['v1'], ['namespace' => 'PriceMonitorPlentyIntegration\Api\Resources'], function ($api)
		// {
        //     $api->post('priceMonitor/filter', 'FilterResource@store');
			
		// });

        $router->get('integrationhome', 'PriceMonitorPlentyIntegration\Controllers\ContentController@home');
        $router->get('loginPriceMonitor', 'PriceMonitorPlentyIntegration\Controllers\ContentController@loginPriceMonitor');
        $router->post('login', 'PriceMonitorPlentyIntegration\Controllers\ContentController@login');
        $router->post('updateContractInfo', 'PriceMonitorPlentyIntegration\Controllers\ContentController@updateContractInfo');
        $router->get('getFilters', 'PriceMonitorPlentyIntegration\Controllers\FilterController@getFilters');
        $router->post('saveFilter', 'PriceMonitorPlentyIntegration\Controllers\FilterController@saveFilter');
        $router->get('getAttributes', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributes');
        $router->get('getAttributeValueByAttrId', 'PriceMonitorPlentyIntegration\Controllers\AttributesController@getAttributeValueByAttrId');
        $router->get('getMappedAttributes', 'PriceMonitorPlentyIntegration\Controllers\AttributesMappingController@getMappedAttributes');
        $router->post('saveAttributesMapping', 'PriceMonitorPlentyIntegration\Controllers\AttributesMappingController@saveAttributesMapping');
        $router->get('getSchedule', 'PriceMonitorPlentyIntegration\Controllers\ProductExportController@getSchedule');
        $router->post('saveSchedule', 'PriceMonitorPlentyIntegration\Controllers\ProductExportController@saveSchedule');
        $router->post('runProductExport', 'PriceMonitorPlentyIntegration\Controllers\ProductExportController@runProductExport');
        $router->post('run', 'PriceMonitorPlentyIntegration\Controllers\SyncController@run');
        $router->get('getTransactionHistory', 'PriceMonitorPlentyIntegration\Controllers\TransactionHistoryController@getTransactionHistory');
        $router->post('saveSchedulePrices', 'PriceMonitorPlentyIntegration\Controllers\PriceImportController@saveSchedulePrices');
        $router->get('getAccountInfo', 'PriceMonitorPlentyIntegration\Controllers\AccountController@getAccountInfo');
        $router->post('saveAccountInfo', 'PriceMonitorPlentyIntegration\Controllers\AccountController@saveAccountInfo');
        $router->post('runPriceImport', 'PriceMonitorPlentyIntegration\Controllers\PriceImportController@runPriceImport');

    } 
}