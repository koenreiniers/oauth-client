<?php
namespace Kr\OAuthClient\Token;

interface TokenInterface
{
    /**
     * Returns the actual value of the token
     *
     * @return boolean
     */
    public function getToken();

    /**
     * Stores the token value
     *
     * @param string $token
     */
    public function setToken($token);

    /**
     * Returns whether or not the token is expired
     *
     * @return boolean
     */
    public function isExpired();

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



    /**
     * Sets the expiry date
     *
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt);

    /**
     * Sets the amount of seconds in which this token will expire
     *
     * @param int $seconds
     */
    public function setExpiresIn($seconds);
}