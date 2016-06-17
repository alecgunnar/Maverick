<?php

namespace Maverick\Middleware;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\Response;

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
}
