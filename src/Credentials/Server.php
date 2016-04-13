<?php
namespace Kr\OAuthClient\Credentials;

class Server extends AbstractCredentials
{
    protected $authUrl, $tokenUrl, $resourceUrl, $grantTypes;

    /**
     * Server constructor.
     * @param string $authUrl
     * @param string $tokenUrl
     * @param string $resourceUrl
     * @param array $grantTypes
     */
    public function __construct($authUrl, $tokenUrl, $resourceUrl, $grantTypes)
    {
        $this->authUrl      = $authUrl;
        $this->tokenUrl     = $tokenUrl;
        $this->resourceUrl  = $resourceUrl;
        $this->grantTypes   = $grantTypes;
    }

    /**
     * Returns whether or not the server supports the grant type
     *
     * @param string $grantType
     *
     * @return bool
     */
    public function supports($grantType)
    {
        return in_array($grantType, $this->grantTypes);
    }

    /**
     * Returns the base authorization url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * Returns the token url
     *
     * @return string
     */
    public function getTokenUrl()
    {
        return $this->tokenUrl;
    }

    /**
     * Returns the base resource url
     *
     * @return string
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }

    public function getCredentials()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "server";
    }

}