<?php
 
namespace PriceMonitorPlentyIntegration\Contracts;
 
use PriceMonitorPlentyIntegration\Models\RunnerToken;
 
/**
 * Class RunnerTokenRepositoryContract
 * @package PriceMonitorPlentyIntegration\Contracts
 */
interface RunnerTokenRepositoryContract
{
    /**
     * saveRunnerToken
     *
     * @param array $data
     * @return void
     */
    public function saveRunnerToken(array $data);

    public function getByToken($token);

}