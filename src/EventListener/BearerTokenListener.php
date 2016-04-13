<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\HttpClient\Events\RequestEvent;
use Kr\HttpClient\Events\ResponseEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Token\BearerToken;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BearerTokenListener implements EventSubscriberInterface
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
        return BearerToken::getType();
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

        if(strtolower($arguments['token_type']) !== strtolower(self::getType())) {
            return;
        }

        if(!isset($arguments['access_token'])) {
            return;
        }

        $expiresAt = null;
        if(isset($arguments['expires_in'])) {
            $expiresIn = $arguments['expires_in'];
            $expiresAt      = (new \DateTime())->modify("+{$expiresIn} seconds");
        }

        $token = new BearerToken($arguments['access_token'], $expiresAt);
        $this->tokenStorage->setAccessToken($token);
    }

    /**
     * Adds Authorization header if the user has a valid bearer token
     *
     * @param RequestEvent $event
     */
    public function onResourceRequest(RequestEvent $event)
    {
        $tokenStorage = $this->tokenStorage;

        $accessToken = $tokenStorage->getAccessToken();
        if($accessToken === null) {
            return;
        }

        if($accessToken->getType() !== self::getType()) {
            return;
        }

        if($accessToken->isExpired()) {
            return;
        }

        $token = $accessToken->getToken();
        $authenticatedRequest = $event->getRequest()->withHeader("Authorization", "Bearer $token");
        $event->setRequest($authenticatedRequest);
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