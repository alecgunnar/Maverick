<?php

namespace Maverick\Router\Route;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Maverick\Router\Route\Route
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetsAttributes()
    {
        $methods = ['GET', 'POST'];
        $path = '/journey/to/mars';
        $handler = function() { return 'MARS'; };

        $instance = new Route($methods, $path, $handler);

        $this->assertAttributeEquals($methods, 'methods', $instance);
        $this->assertAttributeEquals($path, 'path', $instance);
        $this->assertAttributeEquals($handler, 'handler', $instance);
    }

    /**
     * @covers ::setMethods
     */
    public function testSetMethodsSetsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new Route();

        $instance->setMethods($given);

        $this->assertAttributeEquals($expected, 'methods', $instance);
    }

    /**
     * @covers ::setMethods
     */
    public function testSetMethodsReturnsSelf()
    {
        $instance = new Route();

        $ret = $instance->setMethods([]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getMethods
     * @depends testConstructorSetsAttributes
     */
    public function testGetMethodsReturnsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new Route($given);

        $this->assertEquals($expected, $instance->getMethods());
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathSetsPath()
    {
        $given = $expected = '/journey/to/mars';

        $instance = new Route();

        $instance->setPath($given);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathReturnsSelf()
    {
        $instance = new Route();

        $ret = $instance->setPath('/');

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getPath
     * @depends testConstructorSetsAttributes
     */
    public function testGetPathReturnsPath()
    {
        $given = $expected = '/journey/to/mars';

        $instance = new Route([], $given);

        $this->assertEquals($expected, $instance->getPath());
    }

    /**
     * @covers ::setHandler
     */
    public function testSetHandlerSetsHandler()
    {
        $given = $expected = function() { return 'MARS'; };

        $instance = new Route();

        $instance->setHandler($given);

        $this->assertAttributeEquals($expected, 'handler', $instance);
    }

    /**
     * @covers ::setHandler
     */
    public function testSetHandlerReturnsSelf()
    {
        $instance = new Route();

        $ret = $instance->setHandler(function() { });

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getHandler
     * @depends testConstructorSetsAttributes
     */
    public function testGetHandlerReturnsHandler()
    {
        $given = $expected = function() { return 'MARS'; };

        $instance = new Route([], '/', $given);

        $this->assertEquals($expected, $instance->getHandler());
    }
}
