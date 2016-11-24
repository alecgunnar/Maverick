<?php

namespace Maverick\Http\Router\Route\Factory;

use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Maverick\Http\Router\Route\Route;

class ContainerAwareFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCallableIsLoadedFromContainer()
    {
        $method = 'GET';
        $path = '/hello-world';
        $service = 'service.name';
        $callable  = function () { };

        $expected = new Route([$method], $path, $callable);

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($service)
            ->willReturn($callable);

        $instance = new ContainerAwareFactory($container);
        $route = $instance->buildRoute($method, $path, $service);

        $this->assertEquals($expected, $route);
    }

    public function testCallableIsUsedWhenProvided()
    {
        $method = 'GET';
        $path = '/hello-world';
        $callable  = function () { };

        $expected = new Route([$method], $path, $callable);

        $container = $this->getMockContainer();

        $container->expects($this->never())
            ->method('get');

        $instance = new ContainerAwareFactory($container);
        $route = $instance->buildRoute($method, $path, $callable);

        $this->assertEquals($expected, $route);
    }

    public function testMiddlewareLoadedFromContainer()
    {
        $method = 'GET';
        $path = '/hello-world';
        $callable  = function () { };

        $service = 'middleware.service';
        $middleware = function () { };

        $expected = new Route([$method], $path, $callable, [
            $middleware
        ]);

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($service)
            ->willReturn($middleware);

        $instance = new ContainerAwareFactory($container);
        $route = $instance->buildRoute($method, $path, $callable, [
            $service
        ]);

        $this->assertEquals($expected, $route);
    }

    public function testCallableMiddlewareAreUsedWhenProvided()
    {
        $method = 'GET';
        $path = '/hello-world';
        $callable  = function () { };

        $service = 'middleware.service';
        $middleware = function () { };

        $expected = new Route([$method], $path, $callable, [
            $middleware, $middleware
        ]);

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($service)
            ->willReturn($middleware);

        $instance = new ContainerAwareFactory($container);
        $route = $instance->buildRoute($method, $path, $callable, [
            $service, $middleware
        ]);

        $this->assertEquals($expected, $route);
    }

    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }
}
