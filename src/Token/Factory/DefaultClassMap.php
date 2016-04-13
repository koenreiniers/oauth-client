<?php
namespace Kr\OAuthClient\Token\Factory;

use Kr\OAuthClient\Factory\ClassMap\DynamicClassMap;
use Kr\OAuthClient\Token;

class DefaultClassMap extends DynamicClassMap
{
    public function __construct()
    {
        $this->classes = [
            "Bearer"                => Token\BearerToken::class,
            "refresh_token"         => Token\RefreshToken::class,
            "authorization_code"    => Token\AuthorizationCode::class,
            "state"                 => Token\State::class,
        ];
    }
}