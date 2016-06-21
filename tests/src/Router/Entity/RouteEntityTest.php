<?php

namespace Maverick\Router\Entity;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Maverick\Testing\Utility\GenericCallable;

/**
 * @coversDefaultClass Maverick\Router\Entity\RouteEntity
 */
class RouteEntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructorSetsAttributes()
    {
        $methods = ['GET', 'POST'];
        $path = '/journey/to/mars';
        $handler = function() { return 'MARS'; };

        $instance = new RouteEntity($methods, $path, $handler);

        $this->assertAttributeEquals($methods, 'methods', $instance);
        $this->assertAttributeEquals($path, 'path', $instance);
        $this->assertAttributeEquals($handler, 'handler', $instance);
    }

    /**
     * @covers ::withMethods
     */
    public function testWithMethodsSetsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new RouteEntity();

        $instance->withMethods($given);

        $this->assertAttributeEquals($expected, 'methods', $instance);
    }

    /**
     * @covers ::withMethods
     */
    public function testWithMethodsReturnsSelf()
    {
        $instance = new RouteEntity();

        $ret = $instance->withMethods([]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getMethods
     * @depends testConstructorSetsAttributes
     */
    public function testGetMethodsReturnsMethods()
    {
        $given = $expected = ['GET', 'POST'];

        $instance = new RouteEntity($given);

        $this->assertEquals($expected, $instance->getMethods());
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathSetsPath()
    {
        $given = $expected = '/journey/to/mars';

        $instance = new RouteEntity();

        $instance->setPath($given);

        $this->assertAttributeEquals($expected, 'path', $instance);
    }

    /**
     * @covers ::setPath
     */
    public function testSetPathReturnsSelf()
    {
        $instance = new RouteEntity();

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

        $instance = new RouteEntity([], $given);

        $this->assertEquals($expected, $instance->getPath());
    }

    /**
     * @covers ::setHandler
     */
    public function testSetHandlerSetsHandler()
    {
        $given = $expected = function() { return 'MARS'; };

        $instance = new RouteEntity();

        $instance->setHandler($given);

        $this->assertAttributeEquals($expected, 'handler', $instance);
    }

    /**
     * @covers ::setHandler
     */
    public function testSetHandlerReturnsSelf()
    {
        $instance = new RouteEntity();

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

        $instance = new RouteEntity([], '/', $given);

        $this->assertEquals($expected, $instance->getHandler());
    }

    /**
     * @covers ::__invoke
     */
    public function testRouteHandlerIsCalledAsMiddleware()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $handler = $this->getMockBuilder(GenericCallable::class)
            ->getMock();

        $handler->expects($this->once())
            ->method('__invoke')
            ->with($request, $response)
            ->willReturn($response);

        $instance = new RouteEntity();

        $instance->setHandler($handler);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testMiddlewareAreCalled()
    {

    }

    
}
