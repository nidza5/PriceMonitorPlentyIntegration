<?php

use Patagona\Pricemonitor\Core\Interfaces\ConfigService;

class ConfigurationService implements ConfigService
{
    public function getCredentials()
    {
        // return [
        //     'email' => $this->get('email'),
        //     'password' => $this->get('password')
        // ];
    }

    /**
     * Sets clients credentials.
     *
     * @param $email
     * @param $password
     */
    public function setCredentials($email, $password)
    {
        // $this->set(Patagona_Pricemonitor_Helper_Data::XML_PATH_CONFIG_EMAIL, $email);
        // $this->set(Patagona_Pricemonitor_Helper_Data::XML_PATH_CONFIG_PASSWORD, $password);
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
        // $config = $this->configRepository->findOneBy(['configKey' => $key]);

        // return !empty($config) ? $config->getConfigValue() : '';
    }

    /**
     * Create or update config
     * 
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        // $config = $this->configRepository->findOneBy(['configKey' => $key]);

        // if (empty($config)) {
        //     $config = new Config();
        //     $config->setConfigKey($key);
        // }

        // $config->setConfigValue($value);

        // $this->entityManager->persist($config);
        // $this->entityManager->flush();
    }
}


?>