<?php
namespace Kr\OAuthClient\Credentials\Provider;

abstract class AbstractCredentialsProvider implements CredentialsProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getClientCredentials()
    {
        return $this->getCredentials("client_credentials");
    }

    /**
     * @inheritdoc
     */
    public function getServerCredentials()
    {
        return $this->getCredentials("server");
    }
}