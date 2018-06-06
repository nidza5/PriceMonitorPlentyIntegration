<?php

use Patagona\Pricemonitor\Core\Interfaces\ConfigService as ConfigServiceInterface;
use Plenty\Plugin\ConfigRepository;

class ConfigService implements ConfigServiceInterface
{

    /**
     *
     * @var ConfigRepository
     */
    private $config;


    // public function __construct(ConfigRepository $config)
    // {
    //      $this->config = $config;
    // }

    public function getCredentials()
    {
        return [
            'email' => $this->get('email'),
            'password' => $this->get('password')
        ];
    }

    /**
     * Sets clients credentials.
     *
     * @param $email
     * @param $password
     */
    public function setCredentials($email, $password)
    {
          $this->set('email',$email);
          $this->set('password',$password);
    }

    /**
     * Get value from config for given key
     * 
     * @param $key
     * 
     * @return string
     */
    public function get($key)
    {
        // $config = $this->config->get($key);
        // return $config;
    }

    /**
     * Create or update config
     * 
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
       // $this->config->set($key,$value);
    }

    public function getComponentName()
    {
        return $this->get('component_name');
    }

    public function getSource()
    {
        return $this->get('shop_base_url');
    }
}


?>