<?php

namespace Maverick\Http\Router\Route;

use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethodReturnsMethod()
    {
        $given = $expected = ['GET'];

        $instance = new Route($given, '/path', function () { });

        $this->assertEquals($expected, $instance->getMethods());
    }

    public function testGetPathReturnsPath()
    {
        $given = $expected = '/path';

        $instance = new Route(['GET'], $given, function () { });

        $this->assertEquals($expected, $instance->getPath());
    }

    public function testGetCallableReturnsCallable()
    {
        $given = $expected = function () { };

        $instance = new Route(['GET'], '/', $given);

        $this->assertEquals($expected, $instance->getCallable());
    }

    public function testGetMiddlewareReturnsMiddleware()
    {
        $given = $expected = [
            function () { },
            function () { }
        ];

        $instance = new Route(['GET'], '/', function () { }, $given);

        $this->assertEquals($expected, $instance->getMiddleware());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionThrownWhenNonCallableSuppliedAsMiddleware()
    {
        $given = [
            function () { },
            'hello world',
            function () { }
        ];

        $instance = new Route(['GET'], '/', function () { }, $given);
    }
}
