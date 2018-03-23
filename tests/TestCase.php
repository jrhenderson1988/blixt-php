<?php

namespace BlixtTests;

use Mockery as m;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;

class TestCase extends BaseTestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function getInaccessibleProperty($object, $name)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}