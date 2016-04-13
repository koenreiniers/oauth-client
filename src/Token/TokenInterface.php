<?php
namespace Kr\OAuthClient\Token;

interface TokenInterface
{
    /**
     * Returns whether or not the token is expired
     *
     * @return boolean
     */
    public function isExpired();

    /**
     * Returns the actual value of the token
     *
     * @return boolean
     */
    public function getToken();

    /**
     * Returns the exact expiry date of the token
     *
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * Returns the unique type of the token
     *
     * @return string
     */
    public static function getType();
}