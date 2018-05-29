<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use PriceMonitorPlentyIntegration\Models\ProductFilter;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
 
/**
 * Class CreateProductFilterTable
 */
class CreateProductFilterTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(ProductFilter::class);
 
        $this->getLogger("CreateProductFilterTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'ProductFilter']);
 
    }
}