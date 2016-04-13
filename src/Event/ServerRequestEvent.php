<?php
namespace Kr\OAuthClient\Event;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\Event;

class ServerRequestEvent extends Event
{
    /** @var ServerRequestInterface */
    protected $serverRequest;

    /**
     * ServerRequestEvent constructor.
     * @param ServerRequestInterface $serverRequest
     */
    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest()
    {
        return $this->serverRequest;
    }
}