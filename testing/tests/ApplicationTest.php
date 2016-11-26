<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use Maverick\Handler\Error\ErrorHandlerInterface;
use Maverick\Http\Router\RouterInterface;
use Maverick\Http\Router\Route\RouteInterface;
use Maverick\Http\Exception\NotFoundException;
use Maverick\Http\Exception\NotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Container\ContainerInterface;
use UnexpectedValueException;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testConstructEnablesErrorHandler()
    {
        $router = $this->getMockRouter();
        $container = $this->getMockContainer();
        $handler = $this->getMockErrorHandler();

        $handler->expects($this->once())
            ->method('enable');

        $instance = new Application($router, $container, $handler);
    }

    public function testHandleRequestThrowsExceptionIfNoRouteMatchesPath()
    {
        $this->expectException(NotFoundException::class);

        $router = $this->getMockRouter();
        $container = $this->getMockContainer();
        $request = $this->getMockRequest();

        $router->expects($this->once())
            ->method('processRequest')
            ->with($request)
            ->willReturn(RouterInterface::STATUS_NOT_FOUND);

        $instance = new Application($router, $container, $handler);

        $instance->handleRequest($request);
    }

    public function testHandleRequestThrowsExceptionIfNoRouteMatchesPathAndMethod()
    {
        $this->expectException(NotAllowedException::class);
    }

    public function testHandleRequestLooksUpMatchedRouteActionInContainerAndCallsItWithRequest()
    {
        $this->assertTrue(false);
    }

    public function testResponseFromActionReturnedFromHandleRequest()
    {
        $this->assertTrue(false);
    }

    public function testHandleRequestThrowsExceptionIfActionDoesNotReturnResponse()
    {
        $this->expectException(UnexpectedValueException::class);
    }

    public function testSendResponseThrowsExceptionIfHeadersAlreadySent()
    {
        $this->assertTrue(false);
    }

    protected function getMockRouter()
    {
        return $this->getMockBuilder(RouterInterface::class)
            ->getMock();
    }

    protected function getMockRoute()
    {
        return $this->getMockBuilder(RouteInterface::class)
            ->getMock();
    }

    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    protected function getMockErrorHandler()
    {
        return $this->getMockBuilder(ErrorHandlerInterface::class)
            ->getMock();
    }

    protected function getMockRequest()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
    }

    protected function getMockResponse()
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
    }
}
