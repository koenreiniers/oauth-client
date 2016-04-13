<?php
namespace Kr\OAuthClient\EventListener;

use GuzzleHttp\Psr7\Request;
use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\Event\RedirectEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\HttpClient\Events\RequestEvent;
use Kr\OAuthClient\Token\Factory\TokenFactoryInterface;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthorizationCodeListener implements EventSubscriberInterface
{

    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var TokenFactoryInterface */
    protected $tokenFactory;

    public function __construct(CredentialsProviderInterface $credentialsProvider, TokenStorageInterface $tokenStorage, TokenFactoryInterface $tokenFactory)
    {
        $this->credentialsProvider = $credentialsProvider;
        $this->tokenStorage = $tokenStorage;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Add correct credentials to the token request if grant_type is authorization_code
     *
     * @param RequestEvent $event
     */
    public function onTokenRequest(RequestEvent $event)
    {
        if($event->getRequest() !== null) {
            return;
        }
        $credentialsProvider = $this->credentialsProvider;
        $server = $credentialsProvider->getServerCredentials();

        if($server->supports("authorization_code") === false) {
            return;
        }

        $authCode = $this->tokenStorage->getToken("authorization_code");
        if($authCode === null) {
            return;
        }
        if($authCode->isExpired()) {
            return;
        }


        $client = $credentialsProvider->getClientCredentials();
        $code = $authCode->getToken();


        $queryData = [
            "grant_type"    => "authorization_code",
            "code"          => $code,
            "client_id"     => $client->getClientId(),
            "client_secret" => $client->getClientSecret(),
            "redirect_uri"  => $client->getRedirectUri(),
        ];

        $queryString = http_build_query($queryData);

        $uri = $server->getTokenUrl() . "?" . $queryString;

        $request = new Request("GET", $uri);
        $event->setRequest($request);

    }

    /**
     * Looks for the code parameter and stores it in the token storage if present
     *
     * @param ServerRequestEvent $event
     */
    public function onAuthorizationResponse(ServerRequestEvent $event)
    {
        $arguments = $event->getServerRequest()->getQueryParams();

        if(!isset($arguments['code'])) {
            return;
        }

        $expiresIn = 60; // TODO
        $expiresAt = (new \DateTime())->modify("+$expiresIn seconds");



        $token = $this->tokenFactory->create("authorization_code", $arguments['code'], $expiresAt);
        $this->tokenStorage->setToken($token);
    }

    /**
     * Sets response_type to code if the server allows authorization codes
     *
     * @param RedirectEvent $event
     */
    public function onAuthorizationRequest(RedirectEvent $event)
    {
        if($event->getUrl() !== null) {
            return;
        }

        $server = $this->credentialsProvider->getServerCredentials();
        if($server->supports("authorization_code") === false) {
            return;
        }

        $client = $this->credentialsProvider->getClientCredentials();

        $queryData = [
            "client_id" => $client->getClientId(),
            "client_secret" => $client->getClientSecret(),
            "redirect_uri" => $client->getRedirectUri(),
            "response_type" => "code",
        ];
        $queryString = http_build_query($queryData);
        $url = $server->getAuthUrl() . "?" . $queryString;

        $event->setUrl($url);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::TOKEN_REQUEST => ["onTokenRequest", 10],
            OAuthClientEvents::AUTHORIZATION_REQUEST => ["onAuthorizationRequest", 10],
            OAuthClientEvents::AUTHORIZATION_RESPONSE => ["onAuthorizationResponse", 10],
        ];
    }
}