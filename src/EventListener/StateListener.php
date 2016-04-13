<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Event\RedirectEvent;
use Kr\OAuthClient\Manager\TokenManagerInterface;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\Exception\CsrfException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StateListener implements EventSubscriberInterface
{
    /** @var TokenManagerInterface */
    protected $tokenManager;

    public function __construct(TokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Adds CSRF token to the authorization request
     *
     * @param RedirectEvent $event
     */
    public function onAuthorizationRequest(RedirectEvent $event)
    {
        $url = $event->getUrl();
        if($url === null) {
            return;
        }



        $token = md5(uniqid(rand(), true));
        $expiresIn = 120;

        $stateToken = $this->tokenManager->createToken("state");
        $stateToken->setToken($token);
        $stateToken->setExpiresIn($expiresIn);

        $this->tokenManager->persistToken($stateToken);

        $url = $url . "&state=$token";

        $event->setUrl($url);
    }

    /**
     * Validates the CSRF token
     *
     * @param ServerRequestEvent $event
     *
     * @throws CsrfException
     */
    public function onAuthorizationResponse(ServerRequestEvent $event)
    {
        $arguments = $event->getServerRequest()->getQueryParams();

        if(!isset($arguments['state'])) {
            throw new CsrfException();
        }

        $stateToken = $this->tokenManager->findToken("state");

        if($stateToken === null) {
            throw new CsrfException();
        }

        $state = $stateToken->getToken();

        if($state !== $arguments['state']) {
            throw new CsrfException();
        }

        $this->tokenManager->removeToken($stateToken);
    }

    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::AUTHORIZATION_REQUEST => ["onAuthorizationRequest", 1],
            OAuthClientEvents::AUTHORIZATION_RESPONSE => ["onAuthorizationResponse", 10],
        ];
    }
}