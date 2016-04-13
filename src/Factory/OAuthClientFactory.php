<?php
namespace Kr\OAuthClient\Factory;

use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\EventListener\AuthorizationCodeListener;
use Kr\OAuthClient\EventListener\BearerTokenListener;
use Kr\OAuthClient\EventListener\ClientCredentialsListener;
use Kr\OAuthClient\EventListener\ImplicitGrantListener;
use Kr\OAuthClient\EventListener\PasswordListener;
use Kr\OAuthClient\EventListener\RefreshTokenListener;
use Kr\OAuthClient\EventListener\StateListener;
use Kr\OAuthClient\Factory;
use Kr\OAuthClient\OAuthClient;
use GuzzleHttp\ClientInterface;
use Kr\OAuthClient\Token\Factory\TokenFactoryInterface;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OAuthClientFactory
{

    protected $httpClient, $eventDispatcher;

    public function __construct(ClientInterface $httpClient, EventDispatcherInterface $eventDispatcher)
    {
        $this->httpClient = $httpClient;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(CredentialsProviderInterface $credentialsProvider, TokenStorageInterface $tokenStorage, TokenFactoryInterface $tokenFactory)
    {
        // Other
        $this->eventDispatcher->addSubscriber(new StateListener($tokenStorage));

        // Access token
        $this->eventDispatcher->addSubscriber(new BearerTokenListener($credentialsProvider, $tokenStorage, $tokenFactory));

        // Grant listeners
        $this->eventDispatcher->addSubscriber(new ImplicitGrantListener($credentialsProvider, $tokenStorage, $tokenFactory));
        $this->eventDispatcher->addSubscriber(new ClientCredentialsListener($credentialsProvider));
        $this->eventDispatcher->addSubscriber(new RefreshTokenListener($credentialsProvider, $tokenStorage, $tokenFactory));
        $this->eventDispatcher->addSubscriber(new AuthorizationCodeListener($credentialsProvider, $tokenStorage, $tokenFactory));
        $this->eventDispatcher->addSubscriber(new PasswordListener($credentialsProvider));

        return new OAuthClient($this->eventDispatcher, $this->httpClient, $credentialsProvider, $tokenStorage, $tokenFactory);
    }
}