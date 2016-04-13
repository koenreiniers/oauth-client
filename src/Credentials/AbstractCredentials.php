<?php
namespace Kr\OAuthClient\Credentials;

abstract class AbstractCredentials implements CredentialsInterface
{
    /** @var \DateTime  */
    protected $expireAt;

    abstract public function getCredentials();

    public function areExpired()
    {
        if($this->expireAt === null) {
            return false;
        }
        return $this->expireAt < new \DateTime();
    }
}