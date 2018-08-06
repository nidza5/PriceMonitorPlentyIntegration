<?php
namespace PriceMonitorPlentyIntegration\Services;

use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Repositories\Models;
use Plenty\Plugin\Http\Request;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;

class PaymentService {

    private $paymentRepository;

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