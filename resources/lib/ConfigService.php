<?php

use Patagona\Pricemonitor\Core\Interfaces\ConfigService as ConfigServiceInterface;

class ConfigService implements ConfigServiceInterface
{
    private $email;
    private $password;

    public function __construct($email,$password)
    {
         $this->email = $email;
         $this->password = $password;
    }

    public function getCredentials()
    {
        return [
            'email' => $this->email,
            'password' => $this->password
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
        $this->email = $email;
        $this->password = $password;
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