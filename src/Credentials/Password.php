<?php
namespace Kr\OAuthClient\Credentials;

class Password extends AbstractCredentials
{

    protected $username, $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getCredentials()
    {
        return [
            "username"  => $this->getUsername(),
            "password"  => $this->getPassword(),
        ];
    }

    public static function getType()
    {
        return "password";
    }
}