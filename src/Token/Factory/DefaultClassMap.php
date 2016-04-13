<?php
namespace Kr\OAuthClient\Token\Factory;

use Kr\OAuthClient\Factory\ClassMap\AbstractClassMap;
use Kr\OAuthClient\Token;

class DefaultClassMap extends AbstractClassMap
{
    /**
     * @inheritdoc
     */
    protected function registerClassMap()
    {
        return [
            "Bearer"                => Token\BearerToken::class,
            "refresh_token"         => Token\RefreshToken::class,
            "authorization_code"    => Token\AuthorizationCode::class,
            "state"                 => Token\State::class,
        ];
    }
}