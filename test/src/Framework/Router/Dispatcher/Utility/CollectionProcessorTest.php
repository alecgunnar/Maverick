<?php

namespace Maverick\Http\Router\Dispatcher\Utility;

use PHPUnit_Framework_TestCase;
use Maverick\Http\Router\Route\Collection\CollectionInterface;
use Maverick\Http\Router\Route\RouteInterface;
use FastRoute\RouteCollector;

class CollectionProcessorTest extends PHPUnit_Framework_TestCase
{
    public function testRoutesAreAddededToCollectorFromCollection()
    {
        $nameA = 'testRoute1';
        $methodsA = ['GET', 'POST'];
        $pathA = '/hello/world';
        $callableA = function () { };

        $nameB = 'testRoute2';
        $methodsB = ['PUT', 'PATCH'];
        $pathB = '/hello/mars';
        $callableB = function () { };

        $routes = [
            $nameA => ($routeA = $this->getMockRoute($methodsA, $pathA, $callableA)),
            $nameB => ($routeB = $this->getMockRoute($methodsB, $pathB, $callableB))
        ];

        $collection = $this->getMockCollection();
        $collection->expects($this->once())
            ->method('all')
            ->willReturn($routes);

        $collector = $this->getMockCollector();

        $collector->expects($this->exactly(2))
            ->method('addRoute')
            ->withConsecutive(
                [$methodsA, $pathA, $nameA],
                [$methodsB, $pathB, $nameB]
            );

        $instance = new CollectionProcessor($collection);

        $instance($collector);
    }

    protected function getMockCollection()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->getMock();
    }

    protected function getMockCollector()
    {
        return $this->getMockBuilder(RouteCollector::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getMockRoute($method, $path, $callable)
    {
        $route = $this->getMockBuilder(RouteInterface::class)
            ->getMock();

        $route->expects($this->once())
            ->method('getMethods')
            ->willReturn($method);

        $route->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        return $route;
    }
}
