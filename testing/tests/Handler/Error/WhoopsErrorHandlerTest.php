<?php

namespace Maverick\Handler\Error;

use PHPUnit_Framework_TestCase;
use Whoops\RunInterface;

class WhoopsErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testEnableRegistersWhoops()
    {
        $whoops = $this->getMockWhoops();

        $whoops->expects($this->once())
            ->method('register');

        $instance = new WhoopsErrorHandler($whoops);

        $instance->enable();
    }

    public function testDisableUnregistersWhoops()
    {
        $whoops = $this->getMockWhoops();

        $whoops->expects($this->once())
            ->method('unregister');

        $instance = new WhoopsErrorHandler($whoops);

        $instance->disable();
    }

    protected function getMockWhoops()
    {
        return $this->getMockBuilder(RunInterface::class)
            ->getMock();
    }
}
