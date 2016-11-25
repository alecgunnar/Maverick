<?php

namespace Maverick\Http\Router\Route;

use PHPUnit_Framework_TestCase;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethodReturnsMethod()
    {
        $given = $expected = ['GET'];

        $instance = new Route($given, '/path', 'service_name');

        $this->assertEquals($expected, $instance->getMethods());
    }

    public function testGetPathReturnsPath()
    {
        $given = $expected = '/path';

        $instance = new Route(['GET'], $given, 'service_name');

        $this->assertEquals($expected, $instance->getPath());
    }

    public function testGetCallableReturnsCallable()
    {
        $given = $expected = 'service_name';

        $instance = new Route(['GET'], '/', $given);

        $this->assertEquals($expected, $instance->getService());
    }
}
