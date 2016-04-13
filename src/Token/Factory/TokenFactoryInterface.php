<?php
namespace Kr\OAuthClient\Token\Factory;

use Kr\OAuthClient\Factory;
use Kr\OAuthClient\Token\TokenInterface;

interface TokenFactoryInterface
{
    /**
     * Returns a new token instance
     *
     * @param string $type
     * @param string $token
     * @param \DateTime|null $expiresAt
     *
     * @return TokenInterface
     */
    public function create($type, $token, \DateTime $expiresAt = null);
}