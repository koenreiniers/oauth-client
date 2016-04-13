<?php
namespace Kr\OAuthClient\EventListener;

use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\Event\RedirectEvent;
use Kr\OAuthClient\Manager\TokenManagerInterface;
use Kr\OAuthClient\OAuthClientEvents;
use Kr\OAuthClient\Event\ServerRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImplicitGrantListener implements EventSubscriberInterface
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
     * Looks for an already present access_token
     *
     * @param ServerRequestEvent $event
     */
    public function onAuthorizationResponse(ServerRequestEvent $event)
    {
        $arguments = $event->getServerRequest()->getQueryParams();

        if(isset($arguments['access_token']))
        {
            die("TODO: IMPLICIT");
        }
    }

    /**
     * Sets response_type to token if the server allows implicit authorization requests
     *
     * @param RedirectEvent $event
     */
    public function onAuthorizationRequest(RedirectEvent $event)
    {
        if($event->getUrl() !== null) {
            return;
        }

        $server = $this->credentialsProvider->getServerCredentials();
        if(!$server->supports("implicit")) {
            return;
        }

        $client = $this->credentialsProvider->getClientCredentials();

        $queryData = [
            "client_id"         => $client->getClientId(),
            "client_secret"     => $client->getClientSecret(),
            "redirect_uri"      => $client->getRedirectUri(),
            "response_type"     => "token",
        ];
        $queryString = http_build_query($queryData);
        $url = $server->getAuthUrl() . "?" . $queryString;

        $event->setUrl($url);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::AUTHORIZATION_REQUEST => ["onAuthorizationRequest", 128],
            OAuthClientEvents::AUTHORIZATION_RESPONSE => ["onAuthorizationResponse", 128],
        ];
    }
}