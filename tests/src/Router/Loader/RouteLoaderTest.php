<?php

namespace Maverick\Router\Loader;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Interop\Container\ContainerInterface;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

/**
 * @coversDefaultClass Maverick\Router\Loader\RouteLoader
 */
class RouterLoaderTest extends PHPUnit_Framework_TestCase
{
    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    protected function getMockRouteCollection()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsRoutes()
    {
        $given = $expected = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello',
                'handler' => function() { }
            ]
        ];

        $instance = new RouteLoader($this->getMockContainer(), $given);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsContainer()
    {
        $given = $expected = $this->getMockContainer();

        $instance = new RouteLoader($given, []);

        $this->assertAttributeSame($expected, 'container', $instance);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesAddsRoutes()
    {
        $given = $expected = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello',
                'handler' => function() { }
            ]
        ];

    $container = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $instance->withRoutes($given);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesReturnsSelf()
    {
    $container = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $ret = $instance->withRoutes([]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     */
    public function testLoadRoutesAddsRoutesToCollection()
    {
        $name = 'route';
        $methods = ['GET'];
        $path = '/hello';
        $handler = function() { };

        $data = [
            $name => [
                'methods' => $methods,
                'path' => $path,
                'handler' => $handler
            ]
        ];

        $entity = new RouteEntity($methods, $path, $handler);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $container = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     */
    public function testLoadRoutesDefaultsToGetMethod()
    {
        $name = 'route';
        $methods = ['GET'];
        $path = '/hello';
        $handler = function() { };

        $data = [
            $name => [
                'path' => $path,
                'handler' => $handler
            ]
        ];

        $entity = new RouteEntity($methods, $path, $handler);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $container = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     * @expectedException RuntimeException
     * @expectedExceptionMessage A path was not provided for route: route.
     */
    public function testLoadRoutesThrowsExceptionWhenPathNotSet()
    {
        $name = 'route';
        $handler = function() { };

        $data = [
            $name => [
                'handler' => $handler
            ]
        ];

        $collection = $this->getMockRouteCollection();
        $container  = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     * @expectedException RuntimeException
     * @expectedExceptionMessage A handler was not provided for route: route.
     */
    public function testLoadRoutesThrowsExceptionWhenHandlerNotSet()
    {
        $name = 'route';
        $path = '/hello';

        $data = [
            $name => [
                'path' => $path
            ]
        ];

        $collection = $this->getMockRouteCollection();
        $container  = $this->getMockContainer();

        $instance = new RouteLoader($container);

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     */
    public function testLoadRoutesGetsHandlerFromContainerWhenServiceNameProvided()
    {
        $name = 'route';
        $methods = ['GET'];
        $path = '/hello';
        $service = 'test.service';
        $handler = function() { };

        $data = [
            $name => [
                'methods' => $methods,
                'path' => $path,
                'handler' => $service
            ]
        ];

        $entity = new RouteEntity($methods, $path, $handler);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($service)
            ->willReturn($handler);

        $instance = new RouteLoader($container);

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }
}
