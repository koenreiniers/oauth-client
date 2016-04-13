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
     *
     * @return TokenInterface
     */
    public function create($type);
}