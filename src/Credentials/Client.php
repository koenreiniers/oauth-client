<?php
namespace Kr\OAuthClient\Credentials;

class Client extends AbstractCredentials
{
    protected $clientId, $clientSecret, $redirectUri;

    /**
     * Client constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri  = $redirectUri;
    }

    /**
     * @inheritdoc
     */
    public function getCredentials()
    {
        return [
            "client_id"     => $this->getClientId(),
            "client_secret" => $this->getClientSecret(),
            "redirect_uri"  => $this->getRedirectUri(),
        ];
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @return mixed
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "client_credentials";
    }
}