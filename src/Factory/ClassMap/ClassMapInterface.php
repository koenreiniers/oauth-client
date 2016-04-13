<?php
namespace Kr\OAuthClient\Factory\ClassMap;

use Kr\OAuthClient\Token;

interface ClassMapInterface
{
    /**
     * Returns class for alias
     *
     * @return string
     */
    public function getClass($alias);
}