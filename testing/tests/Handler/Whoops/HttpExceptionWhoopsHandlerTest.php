<?php

namespace Maverick\Handler\Whoops;

use PHPUnit_Framework_TestCase;
use Exception;
use Whoops\RunInterface;
use Maverick\Http\Exception\HttpExceptionInterface;

class HttpExceptionWhoopsHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testStatusCodeGetsSetToWhoopsIfHttpExceptionGiven()
    {
        $code = 500;

        $whoops = $this->getMockWhoops();

        $whoops->expects($this->once())
            ->method('sendHttpCode')
            ->with($code);

        $exception = $this->getMockHttpException();

        $exception->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($code);

        $instance = new HttpExceptionWhoopsHandler();

        $instance->setRun($whoops);
        $instance->setException($exception);

        $instance->handle();
    }

    public function testStatusCodeNotSetToWhoopsIfNonHttpExceptionGiven()
    {
        $whoops = $this->getMockWhoops();

        $whoops->expects($this->never())
            ->method('sendHttpCode');

        $exception = $this->getMockException();

        $instance = new HttpExceptionWhoopsHandler();

        $instance->setRun($whoops);
        $instance->setException($exception);

        $instance->handle();
    }

    protected function getMockException()
    {
        return $this->getMockBuilder(Exception::class)
            ->getMock();
    }

    protected function getMockHttpException($code=0)
    {
        return $this->getMockBuilder(HttpExceptionInterface::class)
            ->getMock();
    }

    protected function getMockWhoops()
    {
        return $this->getMockBuilder(RunInterface::class)
            ->getMock();
    }
}
