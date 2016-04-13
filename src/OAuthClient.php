<?php
namespace Kr\OAuthClient;

use GuzzleHttp\Psr7\Response;
use Kr\HttpClient\Events\RequestEvent;
use Kr\HttpClient\Events\ResponseEvent;
use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\Event\RedirectEvent;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\Exception\AuthenticationException;
use Kr\OAuthClient\Exception\AuthorizationException;
use Kr\OAuthClient\Http\ServerRequest;
use Kr\OAuthClient\Token\Factory\TokenFactoryInterface;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OAuthClient
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ClientInterface */
    protected $httpClient;

    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var TokenFactoryInterface */
    protected $tokenFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ClientInterface $httpClient,
        CredentialsProviderInterface $credentialsProvider,
        TokenStorageInterface $tokenStorage,
        TokenFactoryInterface $tokenFactory
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->httpClient = $httpClient;
        $this->credentialsProvider = $credentialsProvider;
        $this->tokenStorage = $tokenStorage;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * Redirect user to authorization url
     *
     * @throws AuthorizationException
     */
    public function startAuthorization()
    {
        /** @var RedirectEvent $event */
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::AUTHORIZATION_REQUEST, new RedirectEvent());

        $url = $event->getUrl();

        if($url === null) {
            throw new AuthorizationException("Failed to generate a redirect_uri");
        }

        header("Location: $url");
        exit;
    }

    /**
     * @throws AuthenticationException
     */
    public function finishAuthorization()
    {
        $serverRequest = new ServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
        $serverRequest = $serverRequest->withQueryParams($_GET);

        $this->eventDispatcher->dispatch(OAuthClientEvents::AUTHORIZATION_RESPONSE, new ServerRequestEvent($serverRequest));


        $accessToken = $this->tokenStorage->getAccessToken();

        if($accessToken !== null && !$accessToken->isExpired()) {
            return;
        }

        $this->requestAccessToken();
    }

    /**
     * @throws AuthenticationException
     */
    public function requestAccessToken()
    {
        /** @var RequestEvent $event */

        $request = new Request("GET", $this->credentialsProvider->getServerCredentials()->getAuthUrl());
        $event = new RequestEvent($request);

        // TODO: Fix this
        $event->setRequest(null);

        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::TOKEN_REQUEST, $event);

        $request = $event->getRequest();

        if($request === null) {
            throw new AuthenticationException("No grant type matched");
        }


        $response = $this->httpClient->send($request);

        $this->eventDispatcher->dispatch(OAuthClientEvents::TOKEN_RESPONSE, new ResponseEvent($response));
    }

    /**
     * Sends request
     *
     * @param string $method
     * @param string $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $url)
    {
        // Check if access token is expired
        $accessToken = $this->tokenStorage->getAccessToken();
        if($accessToken === null || $accessToken->isExpired()) {
            $this->requestAccessToken();
        }

        $server = $this->credentialsProvider->getServerCredentials();
        $url = $server->getResourceUrl() . $url;
        $request = new Request($method, $url);

        /** @var RequestEvent $event */
        $event = new RequestEvent($request);
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::RESOURCE_REQUEST, $event);
        $request = $event->getRequest();

        $response = $this->httpClient->send($request);

        /** @var ResponseEvent $event */
        $event = new ResponseEvent($response);
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::RESOURCE_RESPONSE, $event);
        $response = $event->getResponse();

        return $response;
    }
}