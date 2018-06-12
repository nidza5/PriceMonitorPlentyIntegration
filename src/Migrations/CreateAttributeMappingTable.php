<?php
 
namespace PriceMonitorPlentyIntegration\Migrations;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
use PriceMonitorPlentyIntegration\Models\AttributeMapping;
 
/**
 * Class CreateAttributeMappingTable
 */
class CreateAttributeMappingTable
{
    use Loggable;
 
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(AttributeMapping::class);
 
        $this->getLogger("CreateAttributeMappingTable_run")->debug('PriceMonitorPlentyIntegration::migration.successMessage', ['tableName' => 'AttributeMapping']);
 
    }
}