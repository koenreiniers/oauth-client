<?php
namespace Kr\OAuthClient\Token;

class State extends AbstractToken
{
    /**
     * @inheritdoc
     */
    public static function getType()
    {
        return "state";
    }
}