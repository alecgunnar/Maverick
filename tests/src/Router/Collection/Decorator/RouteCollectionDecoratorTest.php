<?php

namespace Maverick\Router\Collection\Decorator;

use PHPUnit_Framework_TestCase;
use FastRoute\RouteCollector;
use Maverick\Router\Collection\RouteCollectionInterface;
use Maverick\Router\Entity\RouteEntityInterface;

/**
 * @coversDefaultClass Maverick\Router\Collection\Decorator\RouteCollectionDecorator
 */
class RouteCollectionDecoratorTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRouteCollection()
    {
        return $this->getMockBuilder(RouteCollectionInterface::class)
            ->getMock();
    }

    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder(RouteEntityInterface::class)
            ->getMock();
    }

    protected function getInstance($collection)
    {
        return new class($collection) extends RouteCollectionDecorator { };
    }

    /**
     * @covers ::__construct
     */
    public function testConstructSetsCollection()
    {
        $given = $expected = $this->getMockRouteCollection();

        $instance = $this->getInstance($given);

        $this->assertAttributeEquals($expected, 'collection', $instance);
    }

    /**
     * @covers ::withRoute
     */
    public function testWithRouteCallsCollectionsWithRoute()
    {
        $entity = $this->getMockRouteEntity();

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoute')
            ->with($entity)
            ->willReturn($collection);

        $instance = $this->getInstance($collection);

        $instance->withRoute($entity);
    }

    /**
     * @covers ::withRoute
     */
    public function testWithRouteReturnsSelf()
    {
        $instance = $this->getInstance($this->getMockRouteCollection());

        $ret = $instance->withRoute($this->getMockRouteEntity());

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesCallsCollectionsWithRoutes()
    {
        $entity = $this->getMockRouteEntity();

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('withRoutes')
            ->with([$entity])
            ->willReturn($collection);

        $instance = $this->getInstance($collection);

        $instance->withRoutes([$entity]);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesReturnsSelf()
    {
        $instance = $this->getInstance($this->getMockRouteCollection());

        $ret = $instance->withRoutes([$this->getMockRouteEntity()]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::setPrefix
     */
    public function testSetPrefixCallsCollectionsSetPrefix()
    {
        $given = $expected = '/prefix';

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('setPrefix')
            ->with($expected)
            ->willReturn($collection);

        $instance = $this->getInstance($collection);

        $instance->setPrefix($given);
    }

    /**
     * @covers ::setPrefix
     */
    public function testSetPrefixReturnsSelf()
    {
        $instance = $this->getInstance($this->getMockRouteCollection());

        $ret = $instance->setPrefix('/prefix');

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getRoutes
     */
    public function testGetRoutesCallsCollectionsGetRoutes()
    {
        $given = $expected = [$this->getMockRouteEntity()];

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoutes')
            ->willReturn($given);

        $instance = $this->getInstance($collection);

        $this->assertSame($expected, $instance->getRoutes());
    }

    /**
     * @covers ::getRoute
     */
    public function testGetRouteCallsCollectionsGetRouteWithName()
    {
        $name   = 'test.route';
        $entity = $this->getMockRouteEntity();

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('getRoute')
            ->with($name)
            ->willReturn($entity);

        $instance = $this->getInstance($collection);

        $this->assertSame($entity, $instance->getRoute($name));
    }

    /**
     * @covers ::mergeCollection
     */
    public function testMergeCollectionCallsCollectionsMergeCollection()
    {
        $given = $expected = $this->getMockRouteCollection();

        $collection = $this->getMockRouteCollection();

        $collection->expects($this->once())
            ->method('mergeCollection')
            ->with($expected)
            ->willReturn($collection);

        $instance = $this->getInstance($collection);

        $instance->mergeCollection($given);
    }

    /**
     * @covers ::mergeCollection
     */
    public function testMergeCollectionReturnsSelf()
    {
        $instance = $this->getInstance($this->getMockRouteCollection());

        $ret = $instance->mergeCollection($this->getMockRouteCollection());

        $this->assertSame($instance, $ret);
    }
}
