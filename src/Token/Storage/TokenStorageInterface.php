<?php
namespace Kr\OAuthClient\Token\Storage;

use Kr\OAuthClient\Token\TokenInterface;

interface TokenStorageInterface
{
    /**
     * Stores the token in the token storage
     *
     * @param TokenInterface $token
     */
    public function setToken(TokenInterface $token);

    /**
     * Returns token of specified type, null when it does not exist
     *
     * @param string $type
     *
     * @return TokenInterface|null
     */
    public function getToken($type);

    /**
     * Removes the token from storage
     *
     * @param TokenInterface $token
     */
    public function removeToken(TokenInterface $token);
}