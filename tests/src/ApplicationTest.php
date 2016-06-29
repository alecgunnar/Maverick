<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use Interop\Container\ContainerInterface;
use DI\ContainerBuilder;
use DI\Container;
use Maverick\Middleware\RouterMiddleware;
use Maverick\ErrorHandler\ErrorHandlerInterface;
use Maverick\Testing\Middleware\SampleMiddleware;

/**
 * @coversDefaultClass Maverick\Application
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    protected function getSampleMiddleware()
    {
        return $this->getMockBuilder(SampleMiddleware::class)
            ->getMock();
    }

    /**
     * @covers ::withContainer
     */
    public function testWithContainerAddsContainer()
    {
        $given = $this->getMockContainer();

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
        $container = $this->getMockContainer();

        $instance = new Application();

        $ret = $instance->withContainer($container);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getContainers
     */
    public function testGetContainersReturnsListOfContainers()
    {
        $container1 = $this->getMockContainer();
        $container2 = $this->getMockContainer();

        $expected = [
            $container1,
            $container2
        ];

        $instance = new Application();

        $instance->withContainer($container1)
            ->withContainer($container2);

        $this->assertSame($expected, $instance->getContainers());
    }

    /**
     * @covers ::has
     */
    public function testHasChecksContainersInOrder()
    {
        $service = 'test.service';

        $first = $this->getMockContainer();

        $first->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(true);

        $second = $this->getMockContainer();

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
        $first = $this->getMockContainer();

        $first->expects($this->once())
            ->method('has')
            ->with($service)
            ->willReturn(false);

        // Hey, look! It's in the second!
        $second = $this->getMockContainer();

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
        $first = $this->getMockContainer();

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
     * @covers ::loadContainer
     */
    public function testLoadContainerCreatesContainer()
    {
        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expected = [$container];

        $instance = new Application();

        $ret = $instance->loadContainer();

        $this->assertContainsOnlyInstancesOf(ContainerInterface::class, $instance->getContainers());
        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::loadMiddleware
     */
    public function testLoadMiddlewareAddsRequiredMiddlewareFromContainer()
    {
        $routerMiddleware = $this->getSampleMiddleware();
        $responderMiddleware = $this->getSampleMiddleware();

        $expected = [
            $routerMiddleware,
            $responderMiddleware
        ];

        $container = $this->getMockContainer();

        $container->expects($this->any())
            ->method('has')
            ->willReturn(true);

        $container->expects($this->at(1))
            ->method('get')
            ->with('system.middleware.router')
            ->willReturn($routerMiddleware);

        $container->expects($this->at(3))
            ->method('get')
            ->with('system.middleware.response_sender')
            ->willReturn($responderMiddleware);

        $instance = new Application();

        $ret = $instance->withContainer($container)
            ->loadMiddleware();

        $this->assertAttributeSame($expected, 'middleware', $instance);
        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::loadErrorHandler
     */
    public function testLoadsErrorHandlerLoadsErrorHandlerFromContainer()
    {
        $handler = $this->getMockBuilder(ErrorHandlerInterface::class)
            ->getMock();

        $handler->expects($this->once())
            ->method('load');

        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            'system.error_handler' => function() use($handler) {
                return $handler;
            }
        ]);

        $container = $builder->build();

        $instance = new Application();

        $ret = $instance->withContainer($container)
            ->loadErrorHandler();

        $this->assertSame($instance, $ret);
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
