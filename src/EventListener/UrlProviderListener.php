<?php
namespace Kr\OAuthClient\EventListener;

use GuzzleHttp\Psr7\Uri;
use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\HttpClient\Events\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UrlProviderListener implements EventSubscriberInterface
{
    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    public function __construct(CredentialsProviderInterface $credentialsProvider)
    {
        $this->credentialsProvider = $credentialsProvider;
    }

    /**
     * Adds base url to the request uri
     *
     * @param RequestEvent $event
     */
    public function onResourceRequest(RequestEvent $event)
    {
        $resourceUrl = $this->credentialsProvider->getServerCredentials()->getResourceUrl();

        $request = $event->getRequest();
        $uri = (string)$request->getUri();
        if(strpos($uri, $resourceUrl) !== false) {
            return;
        }



        $uri = $resourceUrl . $uri;

        $request = $request->withUri(new Uri($uri));
        $event->setRequest($request);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::RESOURCE_REQUEST => ["onResourceRequest", 10],
        ];
    }
}