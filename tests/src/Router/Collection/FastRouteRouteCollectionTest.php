<?php

namespace Maverick\Router\Collection;

use PHPUnit_Framework_TestCase;
use FastRoute\RouteCollector;
use Maverick\Router\Entity\RouteEntityInterface;

/**
 * @coversDefaultClass Maverick\Router\Collection\FastRouteRouteCollection
 */
class FastRouteRouteCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__invoke
     */
    public function testInvokeAddsRoutesFromCollectionToCollector()
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

        $collector = $this->getMockBuilder(RouteCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collector->expects($this->once())
            ->method('addRoute')
            ->with($method, $path, $entity);

        $instance = new FastRouteRouteCollection();

        $instance->withRoute($entity);

        $instance($collector);
    }
}
