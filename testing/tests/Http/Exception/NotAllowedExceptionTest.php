<?php

namespace Maverick\Http\Exception;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class NotAllowedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testMessageIsGenerated()
    {
        $method = 'GET';

        $expected = sprintf('You are not allowed to make this request via %s.', $method);

        $request = $this->getMockServerRequest($method);

        $instance = new NotAllowedException($request);

        $this->assertEquals($expected, $instance->getMessage());
    }

    public function testGetStatusReturnsCode()
    {
        $instance = new NotAllowedException($this->getMockServerRequest());

        $this->assertEquals(405, $instance->getStatusCode());
    }

    public function testGetRequestReturnsRequest()
    {
        $request = $this->getMockServerRequest();

        $instance = new NotAllowedException($request);

        $this->assertSame($request, $instance->getServerRequest());
    }

    protected function getMockServerRequest(string $method = 'GET')
    {
        $mock = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        return $mock;
    }
}
