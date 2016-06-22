<?php

namespace Maverick\Router;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\Response;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Maverick\Router\Entity\RouteEntityInterface;
use Maverick\Router\Collection\RouteCollectionInterface;

/**
 * @coversDefaultClass Maverick\Router\FastRouteRouter
 */
class FastRouteRouterTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRequest()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
    }

    protected function getMockUri()
    {
        return $this->getMockBuilder(UriInterface::class)
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder(RouteEntityInterface::class)
            ->getMock();
    }

    protected function getMockDispatcher()
    {
        return $this->getMockBuilder(Dispatcher::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsDispatcher()
    {
        $given = $expected = $this->getMockDispatcher();

        $instance = new FastRouteRouter($given);

        $this->assertAttributeSame($expected, 'dispatcher', $instance);
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestReturnsNotFoundWhenNoRouteFound()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::NOT_FOUND]);

        $instance = new FastRouteRouter($dispatcher);
        
        $this->assertEquals(AbstractRouter::ROUTE_NOT_FOUND, $instance->checkRequest($request));
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestReturnsNotAllowedWhenMethodNotAllowed()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, []]);

        $instance = new FastRouteRouter($dispatcher);
        
        $this->assertEquals(AbstractRouter::ROUTE_NOT_ALLOWED, $instance->checkRequest($request));
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestSetsAllowedMethodsWhenMethodNotAllowed()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = ['POST', 'PUT', 'PATCH'];

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, $given]);

        $instance = new FastRouteRouter($dispatcher);

        $instance->checkRequest($request);

        $this->assertAttributeEquals($expected, 'methods', $instance);
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestReturnsRouteFoundWhenRouteMatched()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::FOUND, function() { }, []]);

        $instance = new FastRouteRouter($dispatcher);
        
        $this->assertEquals(AbstractRouter::ROUTE_FOUND, $instance->checkRequest($request));
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestSetsParamsWhenRouteFound()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = [
            'hello' => 'Earth',
            'from'  => 'Mars'
        ];

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::FOUND, function() { }, $given]);

        $instance = new FastRouteRouter($dispatcher);
        
        $instance->checkRequest($request);

        $this->assertAttributeEquals($expected, 'params', $instance);
    }

    /**
     * @covers ::checkRequest
     */
    public function testCheckRequestSetsMatchedRoute()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = $this->getMockRouteEntity();

        $uri = $this->getMockUri();

        $uri->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);

        $dispatcher = $this->getMockDispatcher();

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($method, $path)
            ->willReturn([Dispatcher::FOUND, $given, $given]);

        $instance = new FastRouteRouter($dispatcher);
        
        $instance->checkRequest($request);

        $this->assertAttributeEquals($expected, 'matched', $instance);
    }
}
