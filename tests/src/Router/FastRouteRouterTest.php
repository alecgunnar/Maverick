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
     * @covers ::handleRequest
     */
    public function testHandleRequestReturnsNotFoundHandlerWhenNoRouteFound()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = function() {
            return 'not found!';
        };

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

        $instance->setNotFoundHandler($given);
        
        $this->assertEquals($expected, $instance->handleRequest($request));
    }

    /**
     * @covers ::handleRequest
     */
    public function testHandleRequestReturnsNotAllowedHandlerWhenMethodNotAllowed()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = function() {
            return 'not found!';
        };

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

        $instance->setNotAllowedHandler($given);
        
        $this->assertEquals($expected, $instance->handleRequest($request));
    }

    /**
     * @covers ::handleRequest
     */
    public function testHandleRequestSetsAllowedMethodsWhenMethodNotAllowed()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given    = ['POST', 'PUT', 'PATCH'];
        $expected = [
            AbstractRouter::ALLOWED_METHODS_ATTR => $given
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
            ->willReturn([Dispatcher::METHOD_NOT_ALLOWED, $given]);

        $instance = new FastRouteRouter($dispatcher);

        $instance->setNotAllowedHandler(function() { });

        $instance->handleRequest($request);

        $this->assertAttributeEquals($expected, 'params', $instance);
    }

    /**
     * @covers ::handleRequest
     */
    public function testHandleRequestReturnsHandlerWhenRouteMatched()
    {
        $method = 'GET';
        $path   = '/hello/world';

        $given = $expected = function() {
            return 'not found!';
        };

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
            ->willReturn([Dispatcher::FOUND, $given, []]);

        $instance = new FastRouteRouter($dispatcher);
        
        $this->assertEquals($expected, $instance->handleRequest($request));
    }

    /**
     * @covers ::handleRequest
     */
    public function testHandleRequestSetsParamsWhenRouteFound()
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
        
        $instance->handleRequest($request);

        $this->assertAttributeEquals($expected, 'params', $instance);
    }
}
