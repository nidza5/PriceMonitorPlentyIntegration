<?php

namespace PriceMonitorPlentyIntegration\Services;

use PriceMonitorPlentyIntegration\Contracts\ConfigRepositoryContract;
use PriceMonitorPlentyIntegration\Repositories\ConfigInfoRepository;

class ConfigService 
{ 
    /**
     *
     * @var ConfigRepositoryContract
     */
    private $configInfoRepo;

    // public function __construct(ConfigRepositoryContract $configInfoRepo)
    // {
    //     $this->configInfoRepo = $configInfoRepo;
    // }

    public function __construct()
    {
        
    }

    public function getCredentials()
    {
        $emailObject = $this->configInfoRepo->getConfig('email');
        $passwordObject = $this->configInfoRepo->getConfig('password');
              
        return array(
            'email' => $emailObject->value,
            'password' => $passwordObject->value
        );
    }

    /**
     * Sets clients credentials.
     *
     * @param $email
     * @param $password
     */
    public function setCredentials($email, $password)
    {
        $this->configInfoRepo->saveConfig('email',$email);
        $this->configInfoRepo->saveConfig('password',$password);
    }

     public function getComponentName() {
        return "";
     }

     public function getSource() {
         return "";
     }

     public function get($key) {
     }

     public function set($key, $value) {

     }
}

?>