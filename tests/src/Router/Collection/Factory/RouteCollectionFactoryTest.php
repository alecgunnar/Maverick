<?php

namespace Maverick\Router\Collection\Factory;

use PHPUnit_Framework_TestCase;
use Maverick\Router\Collection\RouteCollection;
use Maverick\Router\Collection\FastRouteRouteCollection;
use Maverick\Router\Collection\ConfigurableRouteCollection;
use Maverick\Router\Entity\Factory\RouteEntityFactory;
use Interop\Container\ContainerInterface;

/**
 * @coversDefaultClass Maverick\Router\Collection\Factory\RouteCollectionFactory
 */
class RouteCollectionFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRouteCollectionFactory()
    {
        return $this->getMockBuilder(RouteCollectionFactory::class)
            ->getMock();
    }

    protected function getMockRouteEntityFactory()
    {
        return $this->getMockBuilder(RouteEntityFactory::class)
            ->getMock();
    }

    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    /**
     * @covers ::build
     */
    public function testBuildCreatesBasicCollectionByDefault()
    {
        $instance = new RouteCollectionFactory();

        $this->assertInstanceOf(RouteCollection::class, $instance->build());
    }

    /**
     * @covers ::build
     */
    public function testBuildCreatesFastRouteCollection()
    {
        $instance = new RouteCollectionFactory();

        $this->assertInstanceOf(FastRouteRouteCollection::class, $instance->build(RouteCollectionFactory::FAST_ROUTE));
    }
}
