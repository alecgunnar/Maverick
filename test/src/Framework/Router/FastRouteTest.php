<?php

namespace Maverick\Http\Router;

use PHPUnit_Framework_TestCase;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;
use Maverick\Http\Router\Route\RouteInterface;

class FastRouteTest extends PHPUnit_Framework_TestCase
{
    public function testMatchingRouteFoundReturnsFound()
    {
        $name = 'route_name';
        $method = 'GET';
        $uri = '/hello/world';

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::FOUND, $name, []]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($this->getMockRoute());

        $instance = new FastRoute($dispatcher, $collection);

        $this->assertEquals(RouterInterface::STATUS_FOUND, $instance->processRequest($request));
    }

    public function testNotMatchingRouteMethodFoundReturnsNotAllowed()
    {
        $method = 'GET';
        $uri = '/hello/world';

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, ['POST']]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();

        $instance = new FastRoute($dispatcher, $collection);

        $this->assertEquals(RouterInterface::STATUS_NOT_ALLOWED, $instance->processRequest($request));
    }

    public function testNoRouteFoundReturnsNotFound()
    {
        $method = 'GET';
        $uri = '/hello/world';

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::NOT_FOUND]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();

        $instance = new FastRoute($dispatcher, $collection);

        $this->assertEquals(RouterInterface::STATUS_NOT_FOUND, $instance->processRequest($request));
    }

    public function testGetRouteReturnsMatchedRoute()
    {
        $name = 'route_name';
        $method = 'GET';
        $uri = '/hello/world';
        $route = $this->getMockRoute();

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::FOUND, $name, []]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRoute($dispatcher, $collection);
        $instance->processRequest($request);

        $this->assertSame($route, $instance->getRoute());
    }

    public function testGetUriVarsReturnsVarsFromMatchedUri()
    {
        $name = 'named_route';
        $method = 'GET';
        $uri = '/hello/world';
        $route = $this->getMockRoute();
        $vars = ['hello' => 'world', 'from' => 'mars'];

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::FOUND, $name, $vars]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($route);

        $instance = new FastRoute($dispatcher, $collection);
        $instance->processRequest($request);

        $this->assertEquals($vars, $instance->getUriVars());
    }

    public function testGetAllowedMethodsReturnsMethodsAllowedByMatchedRoute()
    {
        $method = 'GET';
        $uri = '/hello/world';
        $methods = ['POST', 'PUT'];

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, $methods]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();

        $instance = new FastRoute($dispatcher, $collection);
        $instance->processRequest($request);

        $this->assertEquals($methods, $instance->getAllowedMethods());
    }

    public function testNotFoundIsReturnedByDefault()
    {
        $method = 'GET';
        $uri = '/hello/world';

        $dispatcher = $this->getMockDispatcher();
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $uri)
            ->willReturn([4]);

        $request = $this->getMockRequest($method, $uri);

        $collection = $this->getMockCollection();

        $instance = new FastRoute($dispatcher, $collection);

        $this->assertEquals(RouterInterface::STATUS_NOT_FOUND, $instance->processRequest($request));
    }

    protected function getMockDispatcher()
    {
        return $this->getMockBuilder(Dispatcher::class)
            ->getMock();
    }

    protected function getMockRequest($method, $uri)
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($this->getMockUri($uri));

        return $request;
    }

    protected function getMockUri($uri)
    {
        $mock = $this->getMockBuilder(UriInterface::class)
            ->getMock();

        $mock->expects($this->once())
            ->method('getPath')
            ->willReturn($uri);

        return $mock;
    }

    protected function getMockCollection()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->getMock();
    }

    protected function getMockRoute()
    {
        return $this->getMockBuilder(RouteInterface::class)
            ->getMock();
    }
}
