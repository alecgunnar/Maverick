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

    protected function getMockMiddlewareQueue()
    {
        return $this->getMockBuilder('Maverick\Middleware\Queue\MiddlewareQueueInterface')
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
            ->willReturn($this->getMockMiddlewareQueue());

        $instance = new RouterMiddleware($router);

        $instance($given, new Response(), function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeRunsRouterHandlerQueueWithRequestAndResponse()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $queue = $this->getMockMiddlewareQueue();

        $queue->expects($this->once())
            ->method('__invoke')
            ->with($request, $response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('handleRequest')
            ->willReturn($queue);

        $instance = new RouterMiddleware($router);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsNextWithGivenRequestAndResponseReturnedFromQueue()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $queue = $this->getMockMiddlewareQueue();

        $queue->expects($this->once())
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
            ->willReturn($queue);

        $instance = new RouterMiddleware($router);

        $instance($request, new Response(), $next);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeReturnsResponseFromNext()
    {
        $response = new Response();

        $queue = $this->getMockMiddlewareQueue();

        $queue->expects($this->once())
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
            ->willReturn($queue);

        $instance = new RouterMiddleware($router);

        $ret = $instance(ServerRequest::fromGlobals(), new Response(), $next);

        $this->assertSame($response, $ret);
    }
}
