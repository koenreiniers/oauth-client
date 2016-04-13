<?php
namespace Kr\OAuthClient\Token\Storage;

use Kr\OAuthClient\Token\TokenInterface;

class SessionTokenStorage implements TokenStorageInterface
{
    protected $prefix;

    public function __construct($prefix = "_oauth_tokenstorage_")
    {
        $this->prefix = $prefix;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @inheritdoc
     */
    public function getToken($type)
    {
        if(!isset($_SESSION[$this->prefix.$type])) {
            return null;
        }
        return $_SESSION[$this->prefix.$type];
    }

    /**
     * @inheritdoc
     */
    public function setToken(TokenInterface $token)
    {
        $_SESSION[$this->prefix.$token->getType()] = $token;
    }

    /**
     * @inheritdoc
     */
    public function removeToken(TokenInterface $token)
    {
        unset($_SESSION[$this->prefix.$token->getType()]);
    }
}