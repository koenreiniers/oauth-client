<?php
namespace Kr\OAuthClient\Token;

class RefreshToken extends AbstractToken
{
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "refresh_token";
    }
}