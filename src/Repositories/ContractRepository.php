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

                if($contract != null  && isset($contract->id))
                    $contractId = $contract->id;
            } 

            $contract = $this->getContractById($contractId);

            $contract->priceMonitorId = $contractObject->priceMonitorId;
            $contract->name = $contractObject->name;

            if(!empty($contractObject->salesPricesImport))
                $contract->salesPricesImport = $contractObject->salesPricesImport;
               
             if(isset($contractObject->isInsertSalesPrice))
                $contract->isInsertSalesPrice = $contractObject->isInsertSalesPrice;

            $database->save($contract);

        } catch(\Exception $ex){

            echo $ex->getMessage();

            // To Do Logg exceptions
        }
    }

   /**
     * Update contract information
     *
     * @param array $data
     * @return Contract
     */

    public function updateContract(array $data):Contract
    {
        $database = pluginApp(DataBase::class);

        $contract = pluginApp(Contract::class);

        $priceImportInId = explode(',', $data['salesPricesImport']);

        $contract->id = (int)$data['id'];
        $contract->priceMonitorId = $data['priceMonitorId'];
        $contract->salesPricesImport = $priceImportInId;
        $contract->isInsertSalesPrice = $data['isInsertSalesPrice'];

        echo "ispis";

        if($data['isInsertSalesPrice'] == true)
            echo "true";
        else   
            echo "false";

        $this->saveContract($contract);

       return $contract;
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
        // echo "Price Monitor Id " . $priceMonitorId;

        // if($priceMonitorId == 0 || $priceMonitorId == null || $priceMonitorId == "")
        //     return pluginApp(Contract::class);

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