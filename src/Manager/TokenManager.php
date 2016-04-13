<?php
namespace Kr\OAuthClient\Manager;

use Kr\OAuthClient\Token\Factory\TokenFactoryInterface;
use Kr\OAuthClient\Token\Storage\TokenStorageInterface;
use Kr\OAuthClient\Token\TokenInterface;

class TokenManager implements TokenManagerInterface
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var TokenFactoryInterface */
    protected $tokenFactory;

    /**
     * TokenManager constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param TokenFactoryInterface $tokenFactory
     */
    public function __construct(TokenStorageInterface $tokenStorage, TokenFactoryInterface $tokenFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @inheritdoc
     */
    public function createToken($type)
    {
        return $this->tokenFactory->create($type);
    }

    /**
     * @inheritdoc
     */
    public function findToken($type)
    {
        return $this->tokenStorage->getToken($type);
    }

    /**
     * @inheritdoc
     */
    public function persistToken(TokenInterface $token)
    {
        return $this->tokenStorage->setToken($token);
    }

    /**
     * @inheritdoc
     */
    public function removeToken(TokenInterface $token)
    {
        return $this->tokenStorage->removeToken($token);
    }

}