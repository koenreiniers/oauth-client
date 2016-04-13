<?php
namespace Kr\OAuthClient;

use Kr\HttpClient\Events\RequestEvent;
use Kr\HttpClient\Events\ResponseEvent;
use Kr\OAuthClient\Event\RedirectEvent;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\Exception\AuthenticationException;
use Kr\OAuthClient\Exception\AuthorizationException;
use Kr\OAuthClient\Http\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OAuthClient
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ClientInterface */
    protected $httpClient;

    public function __construct(EventDispatcherInterface $eventDispatcher, ClientInterface $httpClient)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->httpClient = $httpClient;
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

        $this->requestAccessToken();
    }

    /**
     * @param RequestInterface|null $resourceRequest
     *
     * @return RequestInterface|null
     *
     * @throws AuthenticationException
     */
    public function requestAccessToken(RequestInterface $resourceRequest = null)
    {
        /** @var RequestEvent $event */
        $request = new Request("GET", "placeholder");
        $event = new RequestEvent($request);

        // TODO: Fix this
        $event->setRequest(null);

        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::TOKEN_REQUEST, $event);

        $request = $event->getRequest();

        if($request === null) {
            throw new AuthenticationException("No grant type matched");
        }

        $response = $this->httpClient->send($request);


        /** @var ResponseEvent $event */
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::TOKEN_RESPONSE, new ResponseEvent($response));


        if($resourceRequest !== null) {
            /** @var RequestEvent $event */
            $event = new RequestEvent($request);
            $event = $this->eventDispatcher->dispatch(OAuthClientEvents::RESOURCE_REQUEST, $event);
            return $event->getRequest();
        }



        return null;
    }

    /**
     * Sends request
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws AuthenticationException
     */
    public function send(RequestInterface $request)
    {
        /** @var RequestEvent $event */
        $event = new RequestEvent($request);
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::RESOURCE_REQUEST, $event);
        $request = $event->getRequest();

        if(!$request->hasHeader("Authorization")) {
            $request = $this->requestAccessToken($request);
        }

        $response = $this->httpClient->send($request);

        /** @var ResponseEvent $event */
        $event = new ResponseEvent($response);
        $event = $this->eventDispatcher->dispatch(OAuthClientEvents::RESOURCE_RESPONSE, $event);
        $response = $event->getResponse();

        return $response;
    }

    /**
     * Creates a new resource request and sends it to the resource server
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param null $body
     * @param string $protocolVersion
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $uri, array $headers = [], $body = null, $protocolVersion = '1.1')
    {
        $request = new Request($method, $uri, $headers, $body, $protocolVersion);
        return $this->send($request);
    }
}