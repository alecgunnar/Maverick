<?php

namespace Maverick\Router\Collection\Decorator;

use PHPUnit_Framework_TestCase;
use FastRoute\RouteCollector;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

/**
 * @coversDefaultClass Maverick\Router\Collection\Decorator\FastRouteRouteCollectionDecorator
 */
class FastRouteRouteCollectionDecoratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testInvokeAddsRoutesFromCollectionDecoratorToCollector()
    {
        $method  = ['GET'];
        $path    = '/hello/world';

        $entity = $this->getMockBuilder(RouteEntityInterface::class)
            ->getMock();

        $entity->expects($this->once())
            ->method('getMethods')
            ->willReturn($method);

        $entity->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $collection = $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();

        $collection->expects($this->once())
            ->method('getRoutes')
            ->willReturn([$entity]);

        $collector = $this->getMockBuilder(RouteCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collector->expects($this->once())
            ->method('addRoute')
            ->with($method, $path, $entity);

        $instance = new FastRouteRouteCollectionDecorator($collection);

        $instance->withRoute($entity);

        $instance($collector);
    }
}
