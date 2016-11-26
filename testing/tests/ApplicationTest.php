<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;
use Maverick\Handler\Error\ErrorHandlerInterface;
use Maverick\Http\Router\RouterInterface;
use Maverick\Http\Router\Route\Route;
use Maverick\Http\Exception\NotFoundException;
use Maverick\Http\Exception\NotAllowedException;
use Maverick\Controller\RenderableController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
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
        $request = $this->getMockRequest('GET', '/path');

        $router->expects($this->once())
            ->method('processRequest')
            ->with($request)
            ->willReturn(RouterInterface::STATUS_NOT_FOUND);

        $instance = new Application($router, $container);

        $instance->handleRequest($request);
    }

    public function testHandleRequestThrowsExceptionIfNoRouteMatchesPathAndMethod()
    {
        $this->expectException(NotAllowedException::class);

        $router = $this->getMockRouter();
        $container = $this->getMockContainer();
        $request = $this->getMockRequest('GET', '/path');

        $router->expects($this->once())
            ->method('processRequest')
            ->with($request)
            ->willReturn(RouterInterface::STATUS_NOT_ALLOWED);

        $instance = new Application($router, $container);

        $instance->handleRequest($request);
    }

    public function testHandleRequestLooksUpMatchedRouteActionInContainerAndCallsItWithRequest()
    {
        $action = 'action_name';

        $router = $this->getMockRouter();
        $container = $this->getMockContainer();
        $request = $this->getMockRequest('GET', '/path');
        $route = $this->getMockRoute();
        $controller = $this->getMockController();
        $response = $this->getMockResponse();

        $router->expects($this->once())
            ->method('processRequest')
            ->with($request)
            ->willReturn(RouterInterface::STATUS_FOUND);

        $router->expects($this->once())
            ->method('getRoute')
            ->willReturn($route);

        $route->expects($this->once())
            ->method('getService')
            ->willReturn($action);

        $container->expects($this->once())
            ->method('get')
            ->with($action)
            ->willReturn($controller);

        $controller->expects($this->once())
            ->method('__invoke')
            ->with($request)
            ->willReturn($response);

        $instance = new Application($router, $container);

        $this->assertSame($response, $instance->handleRequest($request));
    }

    public function testHandleRequestThrowsExceptionIfActionDoesNotReturnResponse()
    {
        $this->expectException(UnexpectedValueException::class);

        $action = 'action_name';

        $router = $this->getMockRouter();
        $container = $this->getMockContainer();
        $request = $this->getMockRequest('GET', '/path');
        $route = $this->getMockRoute();
        $controller = function() {
            return new \stdClass();
        };

        $router->expects($this->once())
            ->method('processRequest')
            ->with($request)
            ->willReturn(RouterInterface::STATUS_FOUND);

        $router->expects($this->once())
            ->method('getRoute')
            ->willReturn($route);

        $route->expects($this->once())
            ->method('getService')
            ->willReturn($action);

        $container->expects($this->once())
            ->method('get')
            ->with($action)
            ->willReturn($controller);

        $instance = new Application($router, $container);

        $instance->handleRequest($request);
    }

    public function testSendResponseThrowsExceptionIfHeadersAlreadySent()
    {
        
    }

    protected function getMockRouter()
    {
        return $this->getMockBuilder(RouterInterface::class)
            ->getMock();
    }

    protected function getMockRoute()
    {
        return $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
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
    
    protected function getMockRequest($method, $uri)
    {
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();

        $request->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);

        $request->expects($this->any())
            ->method('getUri')
            ->willReturn($this->getMockUri($uri));

        return $request;
    }

    protected function getMockUri($uri)
    {
        $mock = $this->getMockBuilder(UriInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('getPath')
            ->willReturn($uri);

        return $mock;
    }

    protected function getMockResponse()
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->getMock();
    }

    protected function getMockController()
    {
        return $this->getMockBuilder(RenderableController::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
