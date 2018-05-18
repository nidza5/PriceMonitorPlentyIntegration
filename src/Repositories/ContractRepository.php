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
            $contract->priceMonitorId = $contractPricemonitorId;
            $contract->name = $contractName;

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

            $contractId = $contractObject->id;
    
            if($contractId == 0)
            {
                 $contract =  $this->getContractByPriceMonitorId($contractObject->priceMonitorId);

                //  echo json_encode($contract);

                if($contract != null  && isset($contract->id))
                    $contractId = $contract->id;
            } 

            $contract = $this->getContractById($contractId);

            // if($contract == null)
            //    $contract = pluginApp(Contract::class);

            $contract->priceMonitorId = $contractObject->priceMonitorId;
            $contract->name = $contractObject->name;

            if(isset($contractObject->salesPriceImportInId))
                $contract->salesPriceImportInId = $contractObject->salesPriceImportInId;

             if(isset($contractObject->isInsertSalesPrice))
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
           return pluginApp(Contract::class);

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
       
 
        if($priceMonitorId == 0 || $priceMonitorId == null || $priceMonitorId == "")
            return pluginApp(Contract::class);

            //  echo "Price Monitor Id " . $priceMonitorId;

        $databaseContract = pluginApp(DataBase::class);
        $contractOriginal = $databaseContract->query(Contract::class)->where('priceMonitorId', '=', $priceMonitorId)->get();

        // echo json_encode($contractOriginal);

        if($contractOriginal == null)
          return pluginApp(Contract::class);

        return $contractOriginal[0];

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

    public function deleteAllContracts()
    {
        $database = pluginApp(DataBase::class);
 
        $contractsList = $database->query(Contract::class)->get();
 
        foreach($contractsList as $con)
        {
            $database->delete($con);
        }
    }
}