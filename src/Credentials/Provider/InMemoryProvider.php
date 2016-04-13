<?php
namespace Kr\OAuthClient\Credentials\Provider;

use Kr\OAuthClient\Credentials\CredentialsInterface;

class InMemoryProvider extends AbstractCredentialsProvider
{
    /** @var CredentialsInterface[] */
    protected $credentials;

    /**
     * InMemoryProvider constructor.
     * @param CredentialsInterface[] $credentials
     */
    public function __construct(array $credentials)
    {
        foreach($credentials as $credential)
        {
            $this->credentials[$credential->getType()] = $credential;
        }
    }

    /**
     * @inheritdoc
     */
    public function getCredentials($type)
    {
        if(!isset($this->credentials[$type])) {
            return null;
        }
        return $this->credentials[$type];
    }
}