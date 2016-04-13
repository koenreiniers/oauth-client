<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Event\RedirectEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\Exception\CsrfException;
use Kr\OAuthClient\Token\Factory\TokenFactoryInterface;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StateListener implements EventSubscriberInterface
{



    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var TokenFactoryInterface */
    protected $tokenFactory;

    public function __construct(TokenStorageInterface $tokenStorage, TokenFactoryInterface $tokenFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->tokenFactory = $tokenFactory;
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

        $token = md5(uniqid(rand(), true));

        $stateToken = $this->tokenFactory->create("state", $token);

        $tokenStorage->setToken($stateToken);

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