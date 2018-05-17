<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\Contract;
 
/**
 * Class ContractRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface ContractRepositoryContract
{
    /**
     * Save contracts list
     *
     * @param array $data
     * @return void
     */
    public function saveContracts(array $data);

    /**
     * Add a new task to the Contracts list
     *
     * @param array $contract
     * @return void
     */

    public function saveContract(Contract $contract);

 
    /**
     *  Contract list
     *
     * @return Contracts[]
     */
    public function getContracts(): array;

 
/**
     *  Contract list
     *
     * @return Contracts[]
     */
    public function getContractById($id): array;
    

}