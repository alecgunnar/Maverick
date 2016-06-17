<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorAddsAppContainersAndOwnContainer()
    {
        $given = $expected = [
            $this->getMockBuilder('Interop\Container\ContainerInterface')
                ->getMock()
        ];

        $instance = new Application($given);

        $this->assertAttributeEquals($expected, 'containers', $instance);
    }

    public function testInitializeAddsRequiredServices()
    {
        $instance = new Application();

        $instance->initialize();

        $this->assertTrue(
            $instance->has('system.route_collection')
                && $instance->has('system.route_loader')
                && $instance->has('system.router')
                && $instance->has('system.handler.not_found')
                && $instance->has('system.handler.not_allowed')
        );
    }

    public function testIntializedStatuesIsFalseByDefault()
    {
        $instance = new Application();

        $this->assertAttributeEquals(false, 'initialized', $instance);
    }

    public function testInitializeUpdatesInitializedStatusToTrue()
    {
        $instance = new Application();

        $instance->initialize();

        $this->assertAttributeEquals(true, 'initialized', $instance);
    }
}
