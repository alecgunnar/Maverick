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
     * @covers ::with
     */
    public function testwithAddsMiddleware()
    {
        $first = function() { return new Response(); };
        $second = function() { return new Response(); };
        $expected = [$first, $second];

        $instance = $this->getInstance();

        $instance->with($first)
            ->with($second);

        $this->assertAttributeEquals($expected, 'middleware', $instance);
    }

    /**
     * @covers ::with
     */
    public function testwithReturnsSelf()
    {
        $instance = $this->getInstance();

        $ret = $instance->with(function() { });

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getMiddleware
     * @depends testwithAddsMiddleware
     */
    public function testGetMiddlewareReturnsMiddleware()
    {
        $first = function() { return new Response(); };
        $second = function() { return new Response(); };
        $expected = [$first, $second];

        $instance = $this->getInstance();

        $instance->with($first)
            ->with($second);

        $this->assertEquals($expected, $instance->getMiddleware());
    }

    /**
     * @covers ::__invoke
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
            ->with($request, $response, $instance)
            ->willReturn($response);

        $instance->with($middleware);

        $instance($request, $response, function() {
            return new Response();
        });
    }

    /**
     * @covers ::__invoke
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

        $instance->with($middleware);

        $instance(ServerRequest::fromGlobals(), new Response());
    }

    /**
     * @covers ::__invoke
     */
    public function testSuccessiveCallsToinvokeRemovesMiddlewareInQueuedOrder()
    {
        $request  = ServerRequest::fromGlobals();
        $response = new Response();

        $first = function() { return new Response(); };
        $second = function() { return new Response(); };

        $instance = $this->getInstance();

        $instance->with($first)
            ->with($second);

        $instance($request, $response);

        $this->assertAttributeEquals([$second], 'middleware', $instance);

        $instance($request, $response);

        $this->assertAttributeEquals([], 'middleware', $instance);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeReturnsResponseFromMiddleware()
    {
        $given = $expected = new Response();

        $instance = $this->getInstance();

        $instance->with(function() use($given) {
            return $given;
        });

        $this->assertSame($expected, $instance(
            ServerRequest::fromGlobals(),
            new Response()
        ));
    }
}
