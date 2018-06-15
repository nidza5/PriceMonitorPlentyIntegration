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
     * @return string
     */
    public function saveRunnerToken($token): string;

    public function getByToken($token);

}