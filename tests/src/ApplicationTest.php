<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use Maverick\Middleware\RouterMiddleware;

/**
 * @coversDefaultClass Maverick\Application
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::withContainer
     */
    public function testWithContainerAddsContainer()
    {
        $given = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $expected = [$given];

        $instance = new Application();

        $instance->withContainer($given);

        $this->assertAttributeEquals($expected, 'containers', $instance);
    }

    /**
     * @covers ::withContainer
     */
    public function testWithContainerReturnsSelf()
    {
        $container = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $instance = new Application();

        $ret = $instance->withContainer($container);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::has
     */
    public function testHasChecksContainersInOrder()
    {
        $service = 'test.service';

        $first = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $first->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(true);

        $second = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $second->expects($this->never())
            ->method('has');

        $instance = new Application();

        $instance->withContainer($first)
            ->withContainer($second);

        $instance->has($service);
    }

    /**
     * @covers ::has
     */
    public function testHasReturnsFalseWhenServiceDoesNotExist()
    {
        $instance = new Application();

        $this->assertFalse($instance->has('does not exist'));
    }

    /**
     * @covers ::has
     */
    public function testHasRetainsWhichContainerItFoundTheServiceIn()
    {
        $service  = 'test.service';
        $expected = 1;

        // Not going to find it in the first.... :-(
        $first = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $first->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(false);

        // Hey, look! It's in the second!
        $second = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $second->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(true);

        $instance = new Application();

        $instance->withContainer($first)
            ->withContainer($second);

        $instance->has($service);

        $this->assertAttributeEquals($expected, 'foundInContainer', $instance);
    }

    /**
     * @covers ::get
     * @expectedException Interop\Container\Exception\NotFoundException
     * @expectedExceptionMessage The service some.service.which.does.not.exist does not exist.
     */
    public function testGetThrowsExceptionWhenServiceDoesNotExist()
    {
        $instance = new Application();

        $instance->get('some.service.which.does.not.exist');
    }

    /**
     * @covers ::get
     * @depends testHasRetainsWhichContainerItFoundTheServiceIn
     */
    public function testGetReturnsServiceFromCorrectContainer()
    {
        $service  = 'test.service';
        $expected = 'this is the correct return';

        // Not going to find it in the first.... :-(
        $first = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $first->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(false);

        $first->expects($this->never())
            ->method('get')
            ->willReturn('bad bad bad');

        // Hey, look! It's in the second!
        $second = $this->getMockBuilder('Interop\Container\ContainerInterface')
            ->getMock();

        $second->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(true);

        $second->expects($this->once())
            ->method('get')
            ->willReturn($expected);

        $instance = new Application();

        $instance->withContainer($first)
            ->withContainer($second);

        $this->assertEquals($expected, $instance->get($service));
    }

    /**
     * @covers ::initialize
     * @covers ::loadContainer
     * @depends testHasChecksContainersInOrder
     */
    public function testInitializeAddsRequiredServices()
    {
        $instance = new Application();

        $instance->initialize();

        $this->assertTrue(
            $instance->has('system.route_collection')
                && $instance->has('system.route_loader')
                && $instance->has('system.router')
                && $instance->has('system.fast_route.dispatcher')
                && $instance->has('system.fast_route.options')
                && $instance->has('system.handler.not_found')
                && $instance->has('system.handler.not_allowed')
                && $instance->has('system.middleware.router')
                && $instance->has('system.middleware.response_sender')
                && $instance->has('system.config.routes_file')
        );
    }

    /**
     * @covers ::initialize
     * @covers ::loadMiddleware
     */
    public function testInitializeAddsRequiredMiddlewareFromContainer()
    {
        $instance = new Application();

        $instance->initialize();

        $this->assertSame([
            $instance->get('system.middleware.router'),
            $instance->get('system.middleware.response_sender')
        ], $instance->getMiddleware());
    }

    /**
     * @covers ::initialize
     */
    public function testInitializeReturnsSelf()
    {
        $instance = new Application();

        $this->assertSame($instance, $instance->initialize());
    }
}
