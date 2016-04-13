<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\HttpClient\Events\RequestEvent;
use Kr\HttpClient\Events\ResponseEvent;
use Kr\OAuthClient\Manager\TokenManagerInterface;
use Kr\OAuthClient\OAuthClientEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BearerTokenListener implements EventSubscriberInterface
{
    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    /** @var TokenManagerInterface */
    protected $tokenManager;

    public function __construct(CredentialsProviderInterface $credentialsProvider, TokenManagerInterface $tokenManager)
    {
        $this->credentialsProvider = $credentialsProvider;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Looks for a bearer token in the response body
     *
     * @param ResponseEvent $event
     */
    public function onTokenResponse(ResponseEvent $event)
    {
        $body = (string)$event->getResponse()->getBody();
        $arguments = json_decode($body, true);

        if(!isset($arguments['token_type'])) {
            return;
        }

        if(strtolower($arguments['token_type']) !== strtolower("Bearer")) {
            return;
        }

        if(!isset($arguments['access_token'])) {
            return;
        }

        $expiresIn = isset($arguments['expires_in']) ? $arguments['expires_in'] : null;

        $token = $this->tokenManager->createToken("Bearer");
        $token->setToken($arguments['access_token']);
        if($expiresIn !== null) {
            $token->setExpiresIn($expiresIn);
        }
        $this->tokenManager->persistToken($token);
    }

    /**
     * Adds Authorization header if the user has a valid bearer token
     *
     * @param RequestEvent $event
     */
    public function onResourceRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if($request->hasHeader("Authorization")) {

            return;
        }

        $accessToken = $this->tokenManager->findToken("Bearer");

        if($accessToken === null) {
            return;
        }

        if($accessToken->isExpired()) {
            return;
        }

        $token = $accessToken->getToken();

        $request = $request->withHeader("Authorization", "Bearer $token");
        $event->setRequest($request);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::TOKEN_RESPONSE => ["onTokenResponse", 10],
            OAuthClientEvents::RESOURCE_REQUEST => ["onResourceRequest", 10],
        ];
    }
}