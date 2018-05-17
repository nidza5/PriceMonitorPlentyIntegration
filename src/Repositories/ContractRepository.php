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
            
            $contract = new Contract(
                null,
                $contractPricemonitorId,
                $contractName
            );

            $this->saveContract($contract);
        }
    }

    public function saveContract(Contract $contractObject)
    {
        try {

            $database = pluginApp(DataBase::class);
 
            $contract = pluginApp(Contract::class);
    
            $contractId = $contractObject->id;
    
            if($contractId != 0)
            {        
                $originalContract = $this->getContractById($contractId);
                
                if($originalContract->id)
                {   
                    $contract->priceMonitorId = $contractObject->priceMonitorId;
                    $contract->name = $contractObject->name;
                }
            }

            $contract->customerGroupId = $contractObject->customerGroupId;
            $contract->shopImportPriceId = $contractObject->shopImportPriceId;
    
            $database->save($contract);

        } catch(\Exception $ex){
            // To Do Logg exceptions
        }
    }
 
    /**
     * List all items of the To Do list
     *
     * @return Contract[]
     */
    public function getContractById($id): array
    {
        $database = pluginApp(DataBase::class);
        $contractsList = $database->query(Contract::class)->where('id', '=', $id)->get();
        return $contractsList;
    }
 
    /**
     * Update the status of the item
     *
     * @param int $id
     * @return ToDo
     */
    public function getContracts(): array
    {
        $database = pluginApp(DataBase::class);
        $contractsList = $database->query(Contract::class)->get();
        
        return $contractsList;
    }
}