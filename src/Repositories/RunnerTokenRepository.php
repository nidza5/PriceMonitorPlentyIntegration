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
            return "";
            
        $database = pluginApp(DataBase::class);
     
        $tokenModel = pluginApp(RunnerToken::class);

        $tokenModel->token = $token ;

        $database->save($tokenModel);

        return $token;
    }
    

    public function getByToken($token)
    {
        $databaseRunnerToken = pluginApp(DataBase::class);
        $runnerTokenOriginal = $databaseRunnerToken->query(RunnerToken::class)->where('token', '=', $token)->get();

        if($runnerTokenOriginal == null)
            return pluginApp(RunnerToken::class);

      return $runnerTokenOriginal[0];
      
    }

    public function deleteAllTokens()
    {
        $database = pluginApp(DataBase::class);
 
        $tokensList = $database->query(RunnerToken::class)->get();
 
        foreach($tokensList as $con)
        {
            $database->delete($con);
        }

    }

    public function deleteToken($token) 
    {
        $database = pluginApp(DataBase::class);
 
        $runnerTokenOriginal = $database->query(RunnerToken::class)->where('token', '=', $token)->get();

        if($runnerTokenOriginal != null)
           $database->delete($runnerTokenOriginal[0]);
    }
}