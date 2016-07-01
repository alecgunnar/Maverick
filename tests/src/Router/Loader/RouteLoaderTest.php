<?php

namespace Maverick\Router\Loader;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntity;

/**
 * @coversDefaultClass Maverick\Router\Loader\RouteLoader
 */
class RouterLoaderTest extends PHPUnit_Framework_TestCase
{
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
                'path' => '/hello'
            ]
        ];

        $instance = new RouteLoader($given);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesAddsRoutes()
    {
        $given = $expected = [
            'route' => [
                'methods' => ['GET'],
                'path' => '/hello'
            ]
        ];

        $instance = new RouteLoader();

        $instance->withRoutes($given);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesReturnsSelf()
    {
        $instance = new RouteLoader();

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

        $data = [
            $name => [
                'methods' => $methods,
                'path' => $path
            ]
        ];

        $entity = new RouteEntity($methods, $path);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $instance = new RouteLoader();

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

        $data = [
            $name => [
                'path' => $path
            ]
        ];

        $entity = new RouteEntity($methods, $path);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $instance = new RouteLoader();

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

        $data = [
            $name => []
        ];

        $collection = $this->getMockRouteCollection();

        $instance = new RouteLoader();

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     * @covers ::processRoute
     */
    public function testLoadRoutesAddsCallableMiddleware()
    {
        $name = 'route';
        $methods = ['GET'];
        $path = '/hello';
        $middleware = function() { };

        $data = [
            $name => [
                'methods' => $methods,
                'path' => $path,
                'middleware' => [
                    $middleware
                ]
            ]
        ];

        $entity = new RouteEntity($methods, $path);

        $entity->with($middleware);

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity, $name);

        $instance = new RouteLoader();

        $instance->withRoutes($data)
            ->loadRoutes($collection);
    }
}
