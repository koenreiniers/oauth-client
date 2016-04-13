<?php
namespace Kr\OAuthClient\Credentials\Provider;

use Kr\OAuthClient\Credentials\Client;
use Kr\OAuthClient\Credentials\CredentialsInterface;
use Kr\OAuthClient\Credentials\Server;

interface CredentialsProviderInterface
{
    /**
     * Returns credentials or null when not present
     *
     * @param string $type
     *
     * @return CredentialsInterface|null
     */
    public function getCredentials($type);

    /**
     * Returns the server credentials
     *
     * @return Server
     */
    public function getServerCredentials();

    /**
     * Returns the client credentials
     *
     * @return Client
     */
    public function getClientCredentials();
}