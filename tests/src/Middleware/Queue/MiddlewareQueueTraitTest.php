<?php

namespace Maverick\Middleware\Queue;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Maverick\Testing\Middleware\SampleMiddleware;

/**
 * @coversDefaultClass Maverick\Middleware\Queue\MiddlewareQueueTrait
 */
class MiddlewarwQueueTraitTest extends PHPUnit_Framework_TestCase
{
    protected function getInstance()
    {
        return new class implements MiddlewareQueueInterface {
            use MiddlewareQueueTrait;
        };
    }

    /**
     * @covers ::withMiddleware
     */
    public function testWithMiddlewareAddsMiddleware()
    {
        $first = function() { return new Response(); };
        $second = function() { return new Response(); };
        $expected = [$first, $second];

        $instance = $this->getInstance();

        $instance->withMiddleware($first)
            ->withMiddleware($second);

        $this->assertAttributeEquals($expected, 'middleware', $instance);
    }

    /**
     * @covers ::withMiddleware
     */
    public function testWithMiddlewareReturnsSelf()
    {
        $instance = $this->getInstance();

        $ret = $instance->withMiddleware(function() { });

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getMiddleware
     * @depends testWithMiddlewareAddsMiddleware
     */
    public function testGetMiddlewareReturnsMiddleware()
    {
        $first = function() { return new Response(); };
        $second = function() { return new Response(); };
        $expected = [$first, $second];

        $instance = $this->getInstance();

        $instance->withMiddleware($first)
            ->withMiddleware($second);

        $this->assertEquals($expected, $instance->getMiddleware());
    }

    /**
     * @covers ::run
     */
    public function testInvokeCallsUpMiddlewareWithRequestResponseAndNext()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $instance = $this->getInstance();

        $middleware = $this->getMockBuilder(SampleMiddleware::class)
            ->getMock();

        $middleware->expects($this->once())
            ->method('__invoke')
            ->with($request, $response, [$instance, 'run'])
            ->willReturn($response);

        $instance->withMiddleware($middleware);

        $instance->run($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::run
     * @expectedException Maverick\Middleware\Exception\InvalidMiddlewareException
     * @expectedExceptionMessage Middleware did not return an instance of Psr\Http\Message\ResponseInterface
     */
    public function testInvokeThrowsExceptionWhenMiddlewareDoesNotReturnResponse()
    {
        $middleware = $this->getMockBuilder(SampleMiddleware::class)
            ->getMock();

        $middleware->expects($this->once())
            ->method('__invoke');

        $instance = $this->getInstance();

        $instance->withMiddleware($middleware);

        $instance->run(ServerRequest::fromGlobals(), new Response());
    }

    /**
     * @covers ::run
     */
    public function testSuccessiveCallsToinvokeRemovesMiddlewareInQueuedOrder()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $first = function() { return new Response(); };
        $second = function() { return new Response(); };

        $instance = $this->getInstance();

        $instance->withMiddleware($first)
            ->withMiddleware($second);

        $instance->run($request, $response);

        $this->assertAttributeEquals([$second], 'middleware', $instance);

        $instance->run($request, $response);

        $this->assertAttributeEquals([], 'middleware', $instance);
    }

    /**
     * @covers ::run
     */
    public function testInvokeReturnsResponseFromMiddleware()
    {
        $given = $expected = new Response();

        $instance = $this->getInstance();

        $instance->withMiddleware(function() use($given) {
            return $given;
        });

        $this->assertSame($expected, $instance->run(
            ServerRequest::fromGlobals(),
            new Response()
        ));
    }
}
