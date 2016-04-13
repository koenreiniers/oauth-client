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
     * Returns the current access token, null when it does not exist
     *
     * @return TokenInterface
     */
    public function getAccessToken();

    /**
     * Stores the access token
     *
     * @param TokenInterface $token
     */
    public function setAccessToken(TokenInterface $token);

    /**
     * Unsets the token of specified type
     *
     * @param string $type
     */
    public function unsetToken($type);
}