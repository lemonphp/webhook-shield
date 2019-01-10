<?php

namespace Lemon\WebhookShield\Tests;

use InvalidArgumentException;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Trait NonPublicAccessibleTrait
 *
 * Help access to non-public property and method of an object
 *
 * @package     Lemon\WebhookShield\Tests
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
trait NonPublicAccessibleTrait
{
    /**
     * Get a non public property of an object
     *
     * @param object $obj
     * @param string $property
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getNonPublicProperty($obj, $property)
    {
        if (!is_object($obj) || !is_string($property)) {
            return null;
        }
        $ref = new ReflectionProperty(get_class($obj), $property);
        $ref->setAccessible(true);

        return $ref->getValue($obj);
    }

    /**
     * Set value for a non public property of an object
     *
     * @param object $obj
     * @param string $property
     * @param mixed $value
     * @return void
     * @throws \ReflectionException
     */
    protected function setNonPublicProperty($obj, $property, $value)
    {
        if (!is_object($obj) || !is_string($property)) {
            return;
        }
        $ref = new ReflectionProperty(get_class($obj), $property);
        $ref->setAccessible(true);
        $ref->setValue($obj, $value);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $obj Instantiated object that we will run method on.
     * @param string $method Method name to call
     * @param array $params Array of parameters to pass into method.
     * @return mixed         Method return.
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function invokeNonPublicMethod($obj, $method, ...$params)
    {
//        if (!is_object($obj) || !is_string($method)) {
//            throw new InvalidArgumentException();
//        }
        $ref = new ReflectionMethod($obj, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($obj, $params);
    }
}
