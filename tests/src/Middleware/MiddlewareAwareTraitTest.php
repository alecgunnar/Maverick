<?php

namespace Maverick\Middleware;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Maverick\Testing\Middleware\SampleMiddleware;

class MiddlewarwAwareTraitTest extends PHPUnit_Framework_TestCase
{
    protected function getInstance()
    {
        return new class implements MiddlewareAwareInterface {
            use MiddlewareAwareTrait;
        };
    }

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

    public function testWithMiddlewareReturnsSelf()
    {
        $instance = $this->getInstance();

        $ret = $instance->withMiddleware(function() { });

        $this->assertSame($instance, $ret);
    }

    /**
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

    public function testRunCallsUpMiddlewareWithRequestResponseAndNext()
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

        $instance->run($request, $response);
    }

    /**
     * @expectedException Maverick\Middleware\Exception\InvalidMiddlewareException
     * @expectedExceptionMessage Middleware did not return an instance of Psr\Http\Message\ResponseInterface
     */
    public function testRunThrowsExceptionWhenMiddlewareDoesNotReturnResponse()
    {
        $middleware = $this->getMockBuilder(SampleMiddleware::class)
            ->getMock();

        $middleware->expects($this->once())
            ->method('__invoke');

        $instance = $this->getInstance();

        $instance->withMiddleware($middleware);

        $instance->run(
            ServerRequest::fromGlobals(),
            new Response()
        );
    }

    public function testSuccessiveCallsToRunRemovesMiddlewareInQueuedOrder()
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

    public function testRunReturnsResponseFromMiddleware()
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
