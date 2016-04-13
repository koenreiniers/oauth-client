<?php
namespace Kr\OAuthClient\EventListener;

use GuzzleHttp\Psr7\Request;
use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\HttpClient\Events\RequestEvent;
use Kr\HttpClient\Events\ResponseEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Token\RefreshToken;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RefreshTokenListener implements EventSubscriberInterface
{

    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(CredentialsProviderInterface $credentialsProvider, TokenStorageInterface $tokenStorage)
    {
        $this->credentialsProvider = $credentialsProvider;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return RefreshToken::getType();
    }

    /**
     * @param RequestEvent $event
     */
    public function onTokenRequest(RequestEvent $event)
    {
        if($event->getRequest() !== null) {
            return;
        }

        $server = $this->credentialsProvider->getServerCredentials();
        if(!$server->supports("refresh_token")) {
            return;
        }

        $refreshToken = $this->tokenStorage->getToken("refresh_token");
        if($refreshToken === null) {
            return;
        }

        if($refreshToken->isExpired()) {
            return;
        }



        $client = $this->credentialsProvider->getClientCredentials();

        $token = $refreshToken->getToken();

        $requestArgs = [
            "grant_type"    => "refresh_token",
            "refresh_token" => $token,
            "client_id"     => $client->getClientId(),
            "client_secret" => $client->getClientSecret(),
        ];

        $tokenUrl = $server->getTokenUrl();
        $queryString = http_build_query($requestArgs);
        $uri = $tokenUrl . "?" . $queryString;


        $request = new Request("GET", $uri);
        $event->setRequest($request);

    }

    /**
     * Looks for a refresh_token in the response body
     *
     * @param ResponseEvent $event
     */
    public function onTokenResponse(ResponseEvent $event)
    {
        $body = (string)$event->getResponse()->getBody();
        $arguments = json_decode($body, true);

        if(!isset($arguments['refresh_token'])) {
            return;
        }

        $expiresAt = null; // TODO

        $refreshToken = new RefreshToken($arguments['refresh_token'], $expiresAt);
        $this->tokenStorage->setToken($refreshToken);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::TOKEN_REQUEST => ["onTokenRequest", 220],
            OAuthClientEvents::TOKEN_RESPONSE => ["onTokenResponse", 150],
        ];
    }
}