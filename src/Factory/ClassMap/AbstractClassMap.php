<?php
namespace Kr\OAuthClient\Factory\ClassMap;

use Kr\OAuthClient\Exception\InvalidAliasException;
use Kr\OAuthClient\Token;

abstract class AbstractClassMap implements ClassMapInterface
{
    /**
     * @inheritdoc
     */
    abstract protected function registerClassMap();

    public function getClass($alias)
    {
        $map = $this->registerClassMap();
        if(!isset($map[$alias])) {
            throw new InvalidAliasException($alias);
        }
        return $map[$alias];
    }
}