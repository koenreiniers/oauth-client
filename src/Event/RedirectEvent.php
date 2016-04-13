<?php
namespace Kr\OAuthClient\Event;

use Symfony\Component\EventDispatcher\Event;

class RedirectEvent extends Event
{
    /** @var string */
    protected $url;

    /**
     * RedirectEvent constructor.
     * @param null $url
     */
    public function __construct($url = null)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}