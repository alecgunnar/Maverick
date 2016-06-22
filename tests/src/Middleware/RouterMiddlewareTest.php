<?php

namespace Maverick\Middleware;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Maverick\Router\AbstractRouter;
use Maverick\Testing\Utility\GenericCallable;

/**
 * @coversDefaultClass Maverick\Middleware\RouterMiddleware
 */
class RouterMiddlewareTest extends PHPUnit_Framework_TestCase
{
    protected $dumyHandler;

    public function __construct()
    {
        $this->dummyHandler = function() { };
    }

    protected function getMockRouter()
    {
        return $this->getMockBuilder('Maverick\Router\AbstractRouter')
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder('Maverick\Router\Entity\RouteEntityInterface')
            ->getMock();
    }

    protected function getMockCallable()
    {
        return $this->getMockBuilder(GenericCallable::class)
            ->getMock();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsRouter()
    {
        $given = $expected = $this->getMockRouter();

        $instance = new RouterMiddleware($given, $this->dummyHandler, $this->dummyHandler);

        $this->assertAttributeSame($expected, 'router', $instance);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeSendsRequestToRouter()
    {
        $given = $expected = ServerRequest::fromGlobals();

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->with($expected)
            ->willReturn(AbstractRouter::ROUTE_FOUND);

        $instance = new RouterMiddleware($router, $this->dummyHandler, $this->dummyHandler);

        $instance($given, new Response(), function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeRunsMatchedRouteHandlerWithRequestAndResponse()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->with($request, $response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->willReturn(AbstractRouter::ROUTE_FOUND);

        $router->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($route);

        $instance = new RouterMiddleware($router, $this->dummyHandler, $this->dummyHandler);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsNextWithGivenRequestResponseAndParams()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $params = [
            'hello' => 'earth',
            'from' => 'mars'
        ];

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->with($request, $response, $params)
            ->willReturn($response);

        $next = $this->getMockCallable();

        $next->expects($this->once())
            ->method('__invoke')
            ->with($request, $response)
            ->willReturn($response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->willReturn(AbstractRouter::ROUTE_FOUND);

        $router->expects($this->once())
            ->method('getParams')
            ->willReturn($params);

        $router->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($route);

        $instance = new RouterMiddleware($router, $this->dummyHandler, $this->dummyHandler);

        $instance($request, new Response(), $next);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeReturnsResponseFromNextMiddleware()
    {
        $response = new Response();

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->willReturn($response);

        $next = $this->getMockBuilder('Maverick\Testing\Utility\GenericCallable')
            ->getMock();

        $next->expects($this->once())
            ->method('__invoke')
            ->willReturn($response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->willReturn(AbstractRouter::ROUTE_FOUND);

        $router->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($route);

        $instance = new RouterMiddleware($router, $this->dummyHandler, $this->dummyHandler);

        $ret = $instance(ServerRequest::fromGlobals(), new Response(), $next);

        $this->assertSame($response, $ret);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeRunsNotFoundHandlerWithRequestAndResponse()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->willReturn(AbstractRouter::ROUTE_NOT_FOUND);

        $handler = $this->getMockCallable();

        $handler->expects($this->once())
            ->method('__invoke')
            ->with($request, $response);

        $instance = new RouterMiddleware($router, $handler, $this->dummyHandler);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeRunsNotAllowedHandlerWithRequestResponseAndAllowedMethods()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $methods = ['GET', 'POST', 'PUT'];

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->willReturn(AbstractRouter::ROUTE_NOT_ALLOWED);

        $router->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn($methods);

        $handler = $this->getMockCallable();

        $handler->expects($this->once())
            ->method('__invoke')
            ->with($request, $response, $methods);

        $instance = new RouterMiddleware($router, $this->dummyHandler, $handler);

        $instance($request, $response, function() {
            return new Response();
        });
    }
}
