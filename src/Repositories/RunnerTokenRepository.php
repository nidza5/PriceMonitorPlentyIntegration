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
     * @param string $token
     * @return string
     */
    public function saveRunnerToken($token): string
    {
        if($token === null)
            return false;
            
        $database = pluginApp(DataBase::class);
     
        $tokenModel = pluginApp(RunnerToken::class);

        $hashToken = sha1($token);

        $tokenModel->token = $hashToken;

        $database->save();

        return $hashToken;
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