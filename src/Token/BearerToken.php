<?php
namespace Kr\OAuthClient\Token;

class BearerToken extends AbstractToken
{
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "Bearer";
    }
}