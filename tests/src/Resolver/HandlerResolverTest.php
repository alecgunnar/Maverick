<?php

namespace Maverick\Resolver;

use PHPUnit_Framework_TestCase;
use Interop\Container\ContainerInterface;

class HandlerResolverTest extends PHPUnit_Framework_TestCase
{
    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }

    public function getInstance($container = null)
    {
        return new HandlerResolver($container ?? $this->getMockContainer());
    }

    public function testConstructorSetsContainer()
    {
        $given = $expected = $this->getMockContainer();

        $instance = new HandlerResolver($given);

        $this->assertAttributeEquals($expected, 'container', $instance);
    }

    public function testCallablesAreImmediatelyReturned()
    {
        $given = $expected = function() { return 'Call me maybe!?'; };

        $instance = $this->getInstance();

        $instance->resolve($given);

        $this->assertSame($expected, $given);
    }

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
