<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionMethod;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Return private method to test
     */
    public static function getMethod(
        string $className,
        string $methodName
    ): ReflectionMethod {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
