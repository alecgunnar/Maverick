<?php

namespace Maverick\Middleware;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Maverick\Router\AbstractRouter;
use Maverick\Testing\Utility\GenericCallable;
use Maverick\Resolver\ResolverInterface;

/**
 * @coversDefaultClass Maverick\Middleware\RouterMiddleware
 */
class RouterMiddlewareTest extends PHPUnit_Framework_TestCase
{
    protected $dummyHandler;

    public function __construct()
    {
        $this->dummyHandler = function($request, $response) {
            return $response;
        };
    }

    protected function getMockRouter()
    {
        return $this->getMockBuilder('Maverick\Router\AbstractRouter')
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder('Maverick\Router\Entity\RouteEntityInterface')
            ->getMock();
    }

    protected function getMockCallable()
    {
        return $this->getMockBuilder(GenericCallable::class)
            ->getMock();
    }

    protected function getMockResolver()
    {
        return $this->getMockBuilder(ResolverInterface::class)
            ->getMock();
    }

    protected function getMockRequest()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
    }

    protected function getInstance($router = null, $notFound = null, $notAllowed = null, $resolver = null)
    {
        return new RouterMiddleware(
            $router ?? $this->getMockRouter(),
            $notFound ?? $this->dummyHandler,
            $notAllowed ?? $this->dummyHandler,
            $resolver ?? $this->getMockResolver()
        );
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsRouter()
    {
        $given = $expected = $this->getMockRouter();

        $instance = new RouterMiddleware($given, $this->dummyHandler, $this->dummyHandler, $this->getMockResolver());

        $this->assertAttributeSame($expected, 'router', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsNotFoundHandler()
    {
        $given = $expected = function() { return 'handler'; };

        $instance = new RouterMiddleware($this->getMockRouter(), $given, $this->dummyHandler, $this->getMockResolver());

        $this->assertAttributeSame($expected, 'notFoundController', $instance);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsNotAllowedHandler()
    {
        $given = $expected = function() { return 'handler'; };

        $instance = new RouterMiddleware($this->getMockRouter(), $this->dummyHandler, $given, $this->getMockResolver());

        $this->assertAttributeSame($expected, 'notAllowedController', $instance);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsNotFoundHandler()
    {
        $request = ServerRequest::fromGlobals();
        $response = new Response();

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->with($request)
            ->willReturn(AbstractRouter::ROUTE_NOT_FOUND);

        $handler = $this->getMockCallable();

        $handler->expects($this->once())
            ->method('__invoke')
            ->with($request, $response)
            ->willReturn($response);

        $instance = $this->getInstance($router, $handler);

        $instance($request, $response, $this->dummyHandler);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsNotAllowedHandlerWithAllowedMethods()
    {
        $methods = ['GET', 'POST'];

        $request = ServerRequest::fromGlobals();
        $response = new Response();

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->with($request)
            ->willReturn(AbstractRouter::ROUTE_NOT_ALLOWED);

        $router->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn($methods);

        $handler = $this->getMockCallable();

        $requestWithMethods = $request->withAttribute(RouterMiddleware::ALLOWED_METHODS, $methods);

        $handler->expects($this->once())
            ->method('__invoke')
            ->with($requestWithMethods, $response)
            ->willReturn($response);

        $instance = $this->getInstance($router, null, $handler);

        $instance($request, $response, $this->dummyHandler);
    }

    /**
     * @covers ::__invoke
     */
    public function testInvokeCallsRouteHandlerAsMiddlewareWithParams()
    {
        $response = new Response();

        $key = 'hello';
        $value = 'world';
        $next = $this->dummyHandler;

        $params = [
            $key => $value
        ];

        $handler = $this->dummyHandler;

        $request = $this->getMockRequest();

        $request->expects($this->once())
            ->method('withAttribute')
            ->with($key, $value)
            ->willReturn($request);

        $route = $this->getMockRouteEntity();

        $route->expects($this->once())
            ->method('__invoke')
            ->with($request, $response, $next)
            ->willReturn($response);

        $router = $this->getMockRouter();

        $router->expects($this->once())
            ->method('checkRequest')
            ->with($request)
            ->willReturn(AbstractRouter::ROUTE_FOUND);

        $router->expects($this->once())
            ->method('getParams')
            ->willReturn($params);

        $router->expects($this->once())
            ->method('getMatchedRoute')
            ->willReturn($route);

        $instance = $this->getInstance($router, null, null);

        $instance($request, $response, $next);
    }
}
