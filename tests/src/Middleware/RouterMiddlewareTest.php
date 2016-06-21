<?php

namespace Maverick\Middleware;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;

/**
 * @coversDefaultClass Maverick\Middleware\RouterMiddleware
 */
class RouterMiddlewareTest extends PHPUnit_Framework_TestCase
{
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

    /**
     * @covers ::__construct
     */
    public function testConstructSetsRouter()
    {
        $given = $expected = $this->getMockRouter();

        $instance = new RouterMiddleware($given);

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
            ->method('handleRequest')
            ->with($expected)
            ->willReturn($this->getMockRouteEntity());

        $instance = new RouterMiddleware($router);

        $instance($given, new Response(), function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeRunsRouterHandlerrouteWithRequestAndResponse()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->with($request, $response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('handleRequest')
            ->willReturn($route);

        $instance = new RouterMiddleware($router);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsNextWithGivenRequestAndResponseReturnedFromroute()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->willReturn($response);

        $next = $this->getMockBuilder('Maverick\Testing\Utility\GenericCallable')
            ->getMock();

        $next->expects($this->once())
            ->method('__invoke')
            ->with($request, $response)
            ->willReturn($response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('handleRequest')
            ->willReturn($route);

        $instance = new RouterMiddleware($router);

        $instance($request, new Response(), $next);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeReturnsResponseFromNext()
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
            ->method('handleRequest')
            ->willReturn($route);

        $instance = new RouterMiddleware($router);

        $ret = $instance(ServerRequest::fromGlobals(), new Response(), $next);

        $this->assertSame($response, $ret);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeAddsAttributesToRequest()
    {
        $attributes = [
            'a' => 'b',
            'c' => 'd',
            'e' => 'f'
        ];

        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        foreach ($attributes as $k => $v) {
            $request = $request->withAttribute($k, $v);
        }

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->with($request, $this->anything())
            ->willReturn($response);

        $next = $this->getMockBuilder('Maverick\Testing\Utility\GenericCallable')
            ->getMock();

        $next->expects($this->once())
            ->method('__invoke')
            ->willReturn($response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('handleRequest')
            ->willReturn($route);

        $router->expects($this->once())
            ->method('getParams')
            ->willReturn($attributes);

        $instance = new RouterMiddleware($router);

        $ret = $instance(ServerRequest::fromGlobals(), new Response(), $next);
    }
}
