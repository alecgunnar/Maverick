<?php

namespace Maverick\Router\Collection;

use PHPUnit_Framework_TestCase;
use Maverick\Router\Entity\RouteEntity;

/**
 * @coversDefaultClass Maverick\Router\Collection\RouteCollection
 */
class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRouteEntity()
    {
        return $this->getMockBuilder(RouteEntity::class)
            ->getMock();
    }

    /**
     * @covers ::setPrefix
     */
    public function testSetPrefixSetsPrefix()
    {
        $given = $expected = '/prefix';

        $instance = new RouteCollection();

        $instance->setPrefix($given);

        $this->assertAttributeEquals($expected, 'prefix', $instance);
    }

    /**
     * @covers ::setPrefix
     */
    public function testSetPrefixReturnsSelf()
    {
        $instance = new RouteCollection();

        $ret = $instance->setPrefix('/');

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::withRoute
     */
    public function testWithRouteAddUnNamedRoute()
    {
        $given    = new RouteEntity();
        $expected = [$given];

        $instance = new RouteCollection();

        $instance->withRoute($given);

        $this->assertAttributeEquals($expected, 'routes', $instance); 
    }

    /**
     * @covers ::withRoute
     */
    public function testWithRouteAddNamedRoute()
    {
        $name     = 'test';
        $entity   = new RouteEntity();
        $expected = [$name => $entity];

        $instance = new RouteCollection();

        $instance->withRoute($entity, $name);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::withRoute
     * @depends testSetPrefixSetsPrefix
     */
    public function testWithRouteAddsPrefix()
    {
        $given = $expected = '/prefix';

        $entity = $this->getMockRouteEntity();

        $entity->expects($this->once())
            ->method('withPrefix')
            ->with($expected)
            ->willReturn($entity);

        $instance = new RouteCollection();

        $instance->setPrefix($given);

        $instance->withRoute($entity);
    }

    /**
     * @covers ::withRoute
     */
    public function testWithRouteReturnsSelf()
    {
        $instance = new RouteCollection();

        $ret = $instance->withRoute(new RouteEntity());

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesAddsRoutes()
    {
        $given = $expected = [
            'named' => $this->getMockRouteEntity(),
            $this->getMockRouteEntity()
        ];

        $instance = new RouteCollection();

        $instance->withRoutes($given);

        $this->assertAttributeEquals($expected, 'routes', $instance);
    }

    /**
     * @covers ::withRoutes
     */
    public function testWithRoutesReturnsSelf()
    {
        $instance = new RouteCollection();

        $ret = $instance->withRoutes([]);

        $this->assertSame($instance, $ret);
    }

    /**
     * @covers ::getRoutes
     */
    public function testGetRoutesReturnsRoutes()
    {
        $route1 = new RouteEntity();
        $route2 = new RouteEntity();

        $expected = [$route1, $route2];

        $instance = new RouteCollection();

        $instance->withRoute($route1)
            ->withRoute($route2);

        $this->assertSame($expected, $instance->getRoutes());
    }

    /**
     * @coers ::getRoute
     */
    public function testGetRouteReturnsEntityIfItExists()
    {
        $name     = 'test';
        $entity   = new RouteEntity();
        $expected = [$name => $entity];

        $instance = new RouteCollection();

        $instance->withRoute($entity, $name);

        $this->assertSame($entity, $instance->getRoute($name));
    }

    /**
     * @coers ::getRoute
     */
    public function testGetRouteReturnsNullIfItDoesNotExist()
    {
        $instance = new RouteCollection();

        $this->assertNull($instance->getRoute('test'));
    }

    /**
     * @covers ::mergeCollection
     */
    public function testMergeCollectionAddsRoutesFromOtherCollection()
    {
        $route1 = new RouteEntity();
        $route2 = new RouteEntity();

        $expected = [$route1, $route2];

        $collection1 = new RouteCollection();
        $collection2 = new RouteCollection();

        $collection1->withRoute($route1);
        $collection2->withRoute($route2);

        $collection1->mergeCollection($collection2);

        $this->assertAttributeSame($expected, 'routes', $collection1);
    }

    /**
     * @covers ::mergeCollection
     */
    public function testMergeCollectionOverridesRoutesWithSameName()
    {
        $name = 'test';

        $route1 = new RouteEntity();
        $route2 = new RouteEntity();

        $expected = [$name => $route2];

        $collection1 = new RouteCollection();
        $collection2 = new RouteCollection();

        $collection1->withRoute($route1, $name);
        $collection2->withRoute($route2, $name);

        $collection1->mergeCollection($collection2);

        $this->assertAttributeSame($expected, 'routes', $collection1);
    }

    /**
     * @covers ::mergeCollection
     */
    public function testMergeCollectionReturnsSelf()
    {
        $instance = new RouteCollection();

        $ret = $instance->mergeCollection(new RouteCollection());

        $this->assertSame($instance, $ret);
    }
}
