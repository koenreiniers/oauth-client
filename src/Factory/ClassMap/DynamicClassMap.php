<?php
namespace Kr\OAuthClient\Factory\ClassMap;

use Kr\OAuthClientBundle\Entity;

/**
 * Class DynamicClassMap
 * @package Kr\OAuthClient\Factory\ClassMap
 */
class DynamicClassMap extends AbstractClassMap
{
    /** @var array */
    protected $classes = [];

    /**
     * Adds a new class to the class map
     *
     * @param string $alias
     * @param string $class
     */
    public function addClass($alias, $class)
    {
        $this->classes[$alias] = $class;
    }

    /**
     * @inheritdoc
     */
    protected function registerClassMap()
    {
        return $this->classes;
    }
}