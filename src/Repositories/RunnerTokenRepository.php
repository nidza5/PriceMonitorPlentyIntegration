<?php
 
namespace PriceMonitorPlentyIntegration\Repositories;
 
use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use PriceMonitorPlentyIntegration\Contracts\RunnerTokenRepositoryContract;
use PriceMonitorPlentyIntegration\Models\RunnerToken;
 
class RunnerTokenRepository implements RunnerTokenRepositoryContract
{
      /**
     * saveRunnerToken
     *
     * @param array $data
     * @return void
     */
    public function saveRunnerToken(array $data)
    {
       
    }
    

    public function getByToken($token)
    {
        $databaseRunnerToken = pluginApp(DataBase::class);
        $runnerTokenOriginal = $databaseRunnerToken->query(RunnerToken::class)->where('token', '=', $token)->get();

        if($runnerTokenOriginal == null)
            return pluginApp(RunnerToken::class);

      return $runnerTokenOriginal[0];
      
    }
}