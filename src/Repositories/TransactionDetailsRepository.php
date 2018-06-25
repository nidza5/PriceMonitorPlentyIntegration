<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\TransactionDetailsRepositoryContract;
use PriceMonitorPlentyIntegration\Models\TransactionDetails;
use PriceMonitorPlentyIntegration\Constants\FilterType;
 
class TransactionDetailsRepository implements TransactionDetailsRepositoryContract
{
     /**
     * Save
     *
     * @param array $data
     * @return void
     */
    public function saveTransactionDetails(array $data)
    {
        
    }

    public function getAllTransactionDetails()
    {
        $database = pluginApp(DataBase::class);
        $transactionDetails = $database->query(TransactionDetails::class)->get();
        
        return $transactionDetails;
    }

    public function getTransactionHistoryDetailsCount($masterId)
    {
        $database = pluginApp(DataBase::class);
        $transactionDetailsCount = $database->query(TransactionDetails::class)->where('transactionId', '=', $masterId)->count();
        
        return $transactionDetailsCount;
    }

    public function getTransactionHistoryDetailsByFilters($contractId,$transactionId,$uniqueIdentifier,$status) {

        $database = pluginApp(DataBase::class);

        if($status !== null) {
            $transactionHistoryDetails = $database->query(TransactionDetails::class)->where('transactionId', '=', $transactionId)->where('transactionUniqueIdentifier', '=', $uniqueIdentifier)->where('status', '=', $status)->get();
        } else {            
            $transactionHistoryDetails = $database->query(TransactionDetails::class)->where('transactionId', '=', $transactionId)->where('transactionUniqueIdentifier', '=', $uniqueIdentifier)->get();
        }
       
        return $transactionHistoryDetails;
    }

    public function getTransactionHistoryDetailById($id) 
    {
        $database = pluginApp(DataBase::class);
        $transactionHistoryDetail = $database->query(TransactionDetails::class)->where('id', '=', $id)->get();

        if($transactionHistoryDetail == null)
            return pluginApp(TransactionDetails::class);
        else 
            return $transactionHistoryDetail[0];

    }

    public function updateTransactionHistoryDetailsState($transactionDetails, $type,$transactionUniqueIdentifier,$failedItems) 
    {
        $database = pluginApp(DataBase::class);
        
        $savedDetails = array();

        foreach($transactionDetails as $transactionDetail) {
            $detail = $this->getTransactionHistoryDetailById($transactionDetail["id"]);
            $importFailed = false;  

            if (!empty($transactionUniqueIdentifier) && $transactionDetail["masterUniqueIdentifier"] === null) {
                $detail->transactionUniqueIdentifier = $transactionUniqueIdentifier;
            }

            $transactionDetailIdentifier = $transactionDetail["productId"];

            if ($type === FilterType::EXPORT_PRODUCTS) {
                $transactionDetailIdentifier = $transactionDetail["gtin"];
            }

            if (empty($transactionDetailIdentifier)) {
                $detail->gtin = "0";
            }

            foreach ($failedItems as $failedItem) {
                if ($transactionDetailIdentifier == $failedItem["id"]) {
                    $detail->status = $failedItem["status"];
                    $detail->note = $failedItem["errorMessage"];
                    $detail->isUpdated =false;
                    $importFailed = true;
                    break;
                }
            }

            if (!$importFailed && $transactionDetail["id"] !== null) {
                $detail->isUpdated = true;
                $detail->status = "finished";
            }

            $database->save($detail);
            array_push($savedDetails,$detail);  
        }

        return $savedDetails;
    }
}