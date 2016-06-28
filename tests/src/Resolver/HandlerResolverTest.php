<?php

namespace Maverick\Resolver;

use PHPUnit_Framework_TestCase;
use Interop\Container\ContainerInterface;

/**
 * @coversDefaultClass Maverick\Resolver\HandlerResolver
 */
class HandlerResolverTest extends PHPUnit_Framework_TestCase
{
    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    protected function getInstance($container = null)
    {
        return new HandlerResolver($container ?? $this->getMockContainer());
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsContainer()
    {
        $given = $expected = $this->getMockContainer();

        $instance = new HandlerResolver($given);

        $this->assertAttributeEquals($expected, 'container', $instance);
    }

    /**
     * @covers ::resolve
     */
    public function testCallablesAreImmediatelyReturned()
    {
        $given = $expected = function() { return 'Call me maybe!?'; };

        $instance = $this->getInstance();

        $instance->resolve($given);

        $this->assertSame($expected, $given);
    }

    /**
     * @covers ::resolve
     */
    public function testServicesAreLoadedFromContainer()
    {
        $name    = 'service.name';
        $handler = function() { };

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->willReturn($handler);

        $instance = $this->getInstance($container);

        $this->assertSame($handler, $instance->resolve($name));
    }
}
