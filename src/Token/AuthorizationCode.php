<?php
namespace Kr\OAuthClient\Token;

class AuthorizationCode extends AbstractToken
{
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "authorization_code";
    }
}