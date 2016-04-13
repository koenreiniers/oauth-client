<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Event\RedirectEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\Exception\CsrfException;
use Kr\OAuthClient\Token\State;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StateListener implements EventSubscriberInterface
{

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
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

        $tokenStorage = $this->tokenStorage;

        $state = md5(uniqid(rand(), true));
        $stateToken = new State($state);

        $tokenStorage->setToken($stateToken);

        $url = $url . "&state=$state";

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

        $tokenStorage = $this->tokenStorage;

        $stateToken = $tokenStorage->getToken("state");

        if($stateToken === null) {
            throw new CsrfException();
        }

        $state = $stateToken->getToken();

        if($state !== $arguments['state']) {
            throw new CsrfException();
        }

        $tokenStorage->unsetToken("state");
    }

    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::AUTHORIZATION_REQUEST => ["onAuthorizationRequest", 1],
            OAuthClientEvents::AUTHORIZATION_RESPONSE => ["onAuthorizationResponse", 10],
        ];
    }
}