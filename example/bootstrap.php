<?php
$loader = require __DIR__.'/../vendor/autoload.php';

use Kr\OAuthClient\Factory\OAuthClientFactory;
use GuzzleHttp\Client as HttpClient;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Kr\OAuthClient\Credentials;
use Kr\OAuthClient\Token\Storage\SessionTokenStorage;


error_reporting(E_ALL);
ini_set("display_errors", true);

/** @var Credentials\CredentialsInterface[] $credentials */
$credentials = require "config/credentials.php";

$oauthFactory = new OAuthClientFactory(new HttpClient(), new EventDispatcher());

$credentialsProvider = new Credentials\Provider\InMemoryProvider($credentials);
$tokenStorage = new SessionTokenStorage();
$oauth = $oauthFactory->create($credentialsProvider, $tokenStorage);