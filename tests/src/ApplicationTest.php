<?php

namespace Maverick;

use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorAddsBasicDependencies()
    {
        $instance = new Application();

        $this->assertTrue(
            $instance->has('system.route_collection')
                && $instance->has('system.route_loader')
                && $instance->has('system.router')
                && $instance->has('system.handler.not_found')
                && $instance->has('system.handler.not_allowed')
        );
    }
}
