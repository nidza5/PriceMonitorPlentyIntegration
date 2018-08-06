<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use Plenty\Plugin\Http\Request;

class ScheduleExportService {
    /**
     *
     * @var ContractRepositoryContract
     */
    private $contractRepo;

    /**
     *
     * @var ScheduleRepositoryContract
     */
    private $scheduleRepo;
     /**
     * Constructor.
     *
     * @param ContractRepositoryContract $contractRepo
     * @param ScheduleRepositoryContract $scheduleRepo
     */
    public function __construct(ContractRepositoryContract $contractRepo, ScheduleRepositoryContract $scheduleRepo)
    {
        $this->contractRepo = $contractRepo;
        $this->scheduleRepo = $scheduleRepo;
    }

    public function getAdequateScheduleByContract($priceMonitorId)
    {
        $contract = $this->contractRepo->getContractByPriceMonitorId($priceMonitorId);

        if ($contract == null) {
            throw new \Exception("Contract is empty");
        }   

        $scheduleSaved = $this->scheduleRepo->getScheduleByContractId($contract->id);
        
        return $scheduleSaved;
    }
}

?>