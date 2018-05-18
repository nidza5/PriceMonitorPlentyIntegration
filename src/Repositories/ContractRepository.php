<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\ContractRepositoryContract;
use PriceMonitorPlentyIntegration\Models\Contract;
use Plenty\Modules\Frontend\Services\AccountService;
 
class ContractRepository implements ContractRepositoryContract
{
    /**
     * Save contracts
     * @param array $data 
     */
    public function saveContracts(array $data)
    {
        foreach ($data as $contractPricemonitorId => $contractName) {
            
            $contract = pluginApp(Contract::class);
            $contract->contractPricemonitorId = $contractPricemonitorId;
            $contract->contractName = $contractName;

            $this->saveContract($contract);
        }
    }

    public function saveContract(Contract $contractObject)
    {
        try {

            if($contractObject == null)
                return;

            $database = pluginApp(DataBase::class);
 
            $contract = pluginApp(Contract::class);

            $idOfContract = 0;
            $contractId = 0;

            if(property_exists($contractObject,'id') && isset($contractObject->id))
                 $contractId = $contractObject->id;
    
            if($contractId == 0)
            {
                 $contract =  $this->getContractByPriceMonitorId($contractObject->priceMonitorId);

                if($contract != null && property_exists($contract,'id') && isset($contract->id))
                    $contractId = $contract->id;
            } 

            $contract = $this->getContractById($contractId);

            // if($contract == null)
            //    $contract = pluginApp(Contract::class);

            $contract->priceMonitorId = $contractObject->priceMonitorId;
            $contract->name = $contractObject->name;

            if(property_exists($contractObject,'salesPriceImportInId') && isset($contractObject->salesPriceImportInId))
                $contract->salesPriceImportInId = $contractObject->salesPriceImportInId;

             if(property_exists($contractObject,'isInsertSalesPrice') && isset($contractObject->isInsertSalesPrice))
                $contract->isInsertSalesPrice = $contractObject->isInsertSalesPrice;
    
            $database->save($contract);

        } catch(\Exception $ex){
            // To Do Logg exceptions
        }
    }
 
    /**
     * Get contract by id
     *
     * @return Contract
     */
    public function getContractById($id): Contract
    {
        if($id == 0 || $id == null)
            return;

        $database = pluginApp(DataBase::class);
        $contractObject = $database->query(Contract::class)->where('id', '=', $id)->get();
        return $contractObject[0];
    }

     /**
     * Get contract by priceMonitorId
     *
     * @return Contract
     */
     public function getContractByPriceMonitorId($priceMonitorId):Contract
     {
        if($priceMonitorId == 0 || $priceMonitorId == null)
            return null;

        $database = pluginApp(DataBase::class);
        $contractObject = $database->query(Contract::class)->where('priceMonitorId', '=', $priceMonitorId)->get();
        return $contractObject[0];

     }

 
    /**
     * Update the status of the item
     *
     * @return contracts[]
     */
    public function getContracts(): array
    {
        $database = pluginApp(DataBase::class);
        $contractsList = $database->query(Contract::class)->get();
        
        return $contractsList;
    }
}