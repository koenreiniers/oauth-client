<?php
namespace Kr\OAuthClient\Manager;

use Kr\OAuthClient\Token\TokenInterface;

interface TokenManagerInterface
{
    /**
     * Returns a new token instance
     *
     * @param string $type
     *
     * @return TokenInterface
     */
    public function createToken($type);

    /**
     * Fetches token from storage
     *
     * @param string $type
     *
     * @return TokenInterface|null
     */
    public function findToken($type);

    /**
     * Persists the token
     *
     * @param TokenInterface $token
     */
    public function persistToken(TokenInterface $token);

    /**
     * Removes the token
     *
     * @param TokenInterface $token
     */
    public function removeToken(TokenInterface $token);
}