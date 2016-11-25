<?php

namespace Maverick\Http\Exception;

use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class NotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testMessageIsGenerated()
    {
        $method = 'GET';
        $path = '/test';

        $expected = sprintf('A route does not exist for "%s %s".', $method, $path);

        $request = $this->getMockServerRequest($method, $path);

        $instance = new NotFoundException($request);

        $this->assertEquals($expected, $instance->getMessage());
    }

    public function testGetStatusReturnsCode()
    {
        $instance = new NotFoundException($this->getMockServerRequest());

        $this->assertEquals(404, $instance->getStatusCode());
    }

    public function testGetRequestReturnsRequest()
    {
        $request = $this->getMockServerRequest();

        $instance = new NotAllowedException($request);

        $this->assertSame($request, $instance->getServerRequest());
    }

    protected function getMockServerRequest(string $method = 'GET', string $path = '/')
    {
        $mock = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        $mock->expects($this->any())
            ->method('getUri')
            ->willReturn($this->getMockUri($path));

        return $mock;
    }

    protected function getMockUri(string $path = '/')
    {
        $mock = $this->getMockBuilder(UriInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('getPath')
            ->willReturn($path);

        return $mock;
    }
}
