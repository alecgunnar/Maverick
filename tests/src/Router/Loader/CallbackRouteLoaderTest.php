<?php

namespace Maverick\Router\Loader;

use PHPUnit_Framework_TestCase;
use Interop\Container\ContainerInterface;
use Maverick\Router\Collection\Factory\RouteCollectionFactory;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Maverick\Router\Entity\RouteEntityInterface;
use Maverick\Router\Entity\RouteEntity;
use Maverick\Middleware\MiddlewareInterface;
use Maverick\Testing\Utility\GenericCallable;

/**
 * @coversDefaultClass Maverick\Router\Loader\CallbackRouteLoader
 */
class CallbackRouterLoaderTest extends PHPUnit_Framework_TestCase
{
    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    protected function getMockRouteCollectionFactory()
    {
        return $this->getMockBuilder(RouteCollectionFactory::class)
            ->getMock();
    }

    protected function getMockRouteCollection()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();
    }

    protected function getMockRouteEntityFactory()
    {
        return $this->getMockBuilder(RouteEntityFactory::class)
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder(RouteEntityInterface::class)
            ->getMock();
    }

    protected function getMockMiddleware()
    {
        return $this->getMockBuilder(MiddlewareInterface::class)
            ->getMock();
    }

    protected function getMockCallable()
    {
        return $this->getMockBuilder(GenericCallable::class)
            ->getMock();
    }

    protected function getInstance(
        callable $builder = null,
        RouteCollectionFactory $collection = null,
        RouteEntityFactory $entity = null,
        ContainerInterface $container = null,
        string $prefix = null
    ) {
        return new CallbackRouteLoader(
            $builder ?? function() { },
            $collection ?? $this->getMockRouteCollectionFactory(),
            $entity ?? $this->getMockRouteEntityFactory(),
            $container ?? $this->getMockContainer(),
            $prefix
        );
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsBuilder()
    {
        $given = $expected = function() { };

        $instance = new CallbackRouteLoader(
            $given,
            $this->getMockRouteCollectionFactory(),
            $this->getMockRouteEntityFactory(),
            $this->getMockContainer()
        );

        $this->assertAttributeEquals($expected, 'builder', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsCollectionFactory()
    {
        $given = $expected = $this->getMockRouteCollectionFactory();

        $instance = new CallbackRouteLoader(
            function() { },
            $given,
            $this->getMockRouteEntityFactory(),
            $this->getMockContainer()
        );

        $this->assertAttributeEquals($expected, 'collectionFactory', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsEntityFactory()
    {
        $given = $expected = $this->getMockRouteEntityFactory();

        $instance = new CallbackRouteLoader(
            function() { },
            $this->getMockRouteCollectionFactory(),
            $given,
            $this->getMockContainer()
        );

        $this->assertAttributeEquals($expected, 'entityFactory', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsContainer()
    {
        $given = $expected = $this->getMockContainer();

        $instance = new CallbackRouteLoader(
            function() { },
            $this->getMockRouteCollectionFactory(),
            $this->getMockRouteEntityFactory(),
            $given
        );

        $this->assertAttributeEquals($expected, 'container', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsPrefix()
    {
        $given = $expected = '/hello';

        $instance = new CallbackRouteLoader(
            function() { },
            $this->getMockRouteCollectionFactory(),
            $this->getMockRouteEntityFactory(),
            $this->getMockContainer(),
            $given
        );

        $this->assertAttributeEquals($expected, 'prefix', $instance);
    }

    /**
     * @expectedException RuntimeException
     * @covers ::withRoutes
     */
    public function testWithRoutesThrowsException()
    {
        $instance = $this->getInstance();

        $instance->withRoutes([]);
    }

    /**
     * @covers ::get
     */
    public function testGetAddsGetRouteToLoader()
    {
        $methods = ['GET'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->get($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::post
     */
    public function testPostAddsPostRouteToLoader()
    {
        $methods = ['POST'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity(['POST'], $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->post($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::put
     */
    public function testPutAddsPutRouteToLoader()
    {
        $methods = ['PUT'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->put($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::patch
     */
    public function testPatchAddsPatchRouteToLoader()
    {
        $methods = ['PATCH'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->patch($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteAddsDeleteRouteToLoader()
    {
        $methods = ['DELETE'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->delete($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::head
     */
    public function testHeadAddsHeadRouteToLoader()
    {
        $methods = ['HEAD'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->head($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::options
     */
    public function testOptionsAddsOptionsRouteToLoader()
    {
        $methods = ['OPTIONS'];
        $path = '/hello';
        $handler = function() { };
        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->options($path, $handler);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::match
     */
    public function testMatchPullsHandlerFromContainerIfNotAString()
    {
        $methods = ['GET'];
        $path    = '/hello/world';
        $service = 'test.service';
        $handler = function() { };

        $expected = new RouteEntity($methods, $path, $handler);

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($service)
            ->willReturn($handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity, $container);

        $ret = $instance->match($methods, $path, $service);

        $this->assertAttributeEquals([$expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::match
     */
    public function testMatchAddsRouteWithName()
    {
        $name    = 'route.name';
        $methods = ['GET'];
        $path    = '/hello/world';
        $service = 'test.service';
        $handler = function() { };

        $expected = new RouteEntity($methods, $path, $handler);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->with($methods, $path, $handler)
            ->willReturn($expected);

        $instance = $this->getInstance(null, null, $entity);

        $ret = $instance->match($methods, $path, $handler, $name);

        $this->assertAttributeEquals([$name => $expected], 'routes', $instance);
        $this->assertSame($expected, $ret);
    }

    /**
     * @covers ::group
     */
    public function testGroupCreatesAndAddsLoader()
    {
        $builder = function() { };
        $collection = $this->getMockRouteCollectionFactory();
        $entity = $this->getMockRouteEntityFactory();
        $container = $this->getMockContainer();
        $prefix = '/this/is/a/prefix';

        $expected = $this->getInstance(
            $builder,
            $collection,
            $entity,
            $container,
            $prefix
        );

        $instance = $this->getInstance(
            null,
            $collection,
            $entity,
            $container,
            $prefix
        );

        $instance->group($prefix, $builder);

        $this->assertAttributeEquals([$expected], 'loaders', $instance);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRoutesCallsBuilder()
    {
        $builder = $this->getMockCallable();

        $instance = $this->getInstance($builder);

        $builder->expects($this->once())
            ->method('__invoke')
            ->with($instance);

        $collection = $this->getMockRouteCollection();

        $instance->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRoutesAddsMiddlewareToRoutes()
    {
        $methods = ['GET'];
        $path = '/path';
        $handler = function() { };
        $name = 'route.name';
        $middleware = $this->getMockMiddleware();

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('withMiddlewares')
            ->with([$middleware]);

        $collection = $this->getMockRouteCollection();

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->willReturn($route);

        $builder = function($loader) {
            $loader->match(['GET'], '/', function() { });
        };

        $instance = $this->getInstance($builder, null, $entity);

        $instance->withMiddleware($middleware);

        $instance->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRoutesAddsPrefixToCollection()
    {
        $prefix = '/prefix';

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('setPrefix')
            ->with($prefix)
            ->willReturn($collection);

        $instance = $this->getInstance(null, null, null, null, $prefix);

        $instance->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRoutesAddsRoutesToCollection()
    {
        $methods = ['GET'];
        $path = '/path';
        $handler = function() { };
        $name = 'route.name';

        $route = new RouteEntity($methods, $path, $handler);

        $expected = [$name => $route];

        $builder = function($loader) use($methods, $path, $handler, $name) {
            $loader->match($methods, $path, $handler, $name);
        };

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoutes')
            ->with($expected)
            ->willReturn($collection);

        $entity = $this->getMockRouteEntityFactory();

        $entity->expects($this->once())
            ->method('build')
            ->willReturn($route);

        $instance = $this->getInstance($builder, null, $entity);

        $instance->loadRoutes($collection);
    }

    /**
     * @covers ::loadRoutes
     */
    public function testLoadRunsOtherCollectionsAndMergesNewCollection()
    {
        $groupCollection = $this->getMockRouteCollection();

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('mergeCollection')
            ->with($groupCollection)
            ->willReturn($collection);

        $factory = $this->getMockRouteCollectionFactory();

        $factory->expects($this->once())
            ->method('build')
            ->willReturn($groupCollection);

        $instance = $this->getInstance(null, $factory);

        $instance->group('/prefix', function() { });

        $instance->loadRoutes($collection);
    }
}
