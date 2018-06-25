<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use PriceMonitorPlentyIntegration\Contracts\ScheduleRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ScheduleRepository;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ContractRepository;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;

class PaymentService {
    /**
     *
     * @var ContractRepositoryContract
     */
    private $contractRepo;

    private $paymentRepository;

    public function __construct(ContractRepositoryContract $contractRepo)
    {
        $this->contractRepo = $contractRepo;
    }

    public function getAllPayment()
    {
        $repository = pluginApp(PaymentRepositoryContract::class);       

        $authHelper = pluginApp(AuthHelper::class);

        $payments = null;

        $payments = $authHelper->processUnguarded(
            function () use ($repository, $payments) {
            
                return $repository->getAll();
            }
        );

    

        return $payments;
     
    }
}

?>