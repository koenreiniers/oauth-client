<?php
namespace Kr\OAuthClient\EventListener;

use GuzzleHttp\Psr7\Request;
use Kr\HttpClient\Events\RequestEvent;
use Kr\OAuthClient\Credentials\Provider\CredentialsProviderInterface;
use Kr\OAuthClient\OAuthClientEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordListener implements EventSubscriberInterface
{


    /** @var CredentialsProviderInterface */
    protected $credentialsProvider;

    public function __construct(CredentialsProviderInterface $credentialsProvider)
    {
        $this->credentialsProvider = $credentialsProvider;
    }

    /**
     * @param RequestEvent $event
     */
    public function onTokenRequest(RequestEvent $event)
    {
        if($event->getRequest() !== null) {
            return;
        }

        $server = $this->credentialsProvider->getServerCredentials();
        if(!$server->supports("password")) {
            return;
        }

        $credentials = $this->credentialsProvider->getCredentials("password");
        if($credentials === null) {
            return;
        }
        if($credentials->areExpired()) {
            return;
        }


        $client = $this->credentialsProvider->getClientCredentials();


        $requestArgs = [
            "grant_type"    => "password",
            "username"      => $credentials->getUsername(),
            "password"      => $credentials->getPassword(),
            "client_id"     => $client->getClientId(),
            "client_secret" => $client->getClientSecret(),
        ];

        $tokenUrl = $server->getTokenUrl();
        $queryString = http_build_query($requestArgs);
        $uri = $tokenUrl . "?" . $queryString;


        $request = new Request("GET", $uri);
        $event->setRequest($request);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            OAuthClientEvents::TOKEN_REQUEST => ["onTokenRequest", 20],
        ];
    }
}