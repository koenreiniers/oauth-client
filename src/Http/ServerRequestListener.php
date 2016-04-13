<?php
namespace Kr\OAuthClient\Http;

use Kr\OAuthClient\Event\ServerRequestEvent;
use Kr\OAuthClient\OAuthClientEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServerRequestListener
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function listen()
    {
        $serverRequest = ServerRequest::createFromGlobals();
        $this->eventDispatcher->dispatch(OAuthClientEvents::SERVER_REQUEST, new ServerRequestEvent($serverRequest));
    }
}