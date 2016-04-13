<?php
namespace Kr\OAuthClient\Token\Factory;

use Kr\OAuthClient\Factory\ClassMap\ClassMapInterface;
use Kr\OAuthClient\Factory;
use Kr\OAuthClient\Token\TokenInterface;

class TokenFactory implements TokenFactoryInterface
{
    /** @var ClassMapInterface */
    protected $classMap;

    /**
     * TokenFactory constructor.
     * @param ClassMapInterface $classMap
     */
    public function __construct(ClassMapInterface $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * Returns a new token instance
     *
     * @param string $type
     * @param string $token
     * @param \DateTime|null $expiresAt
     *
     * @return TokenInterface
     */
    public function create($type, $token, \DateTime $expiresAt = null)
    {
        $class = $this->classMap->getClass($type);

        return new $class($type, $token, $expiresAt);
    }
}