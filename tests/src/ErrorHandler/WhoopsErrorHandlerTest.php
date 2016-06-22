<?php

namespace Maverick\ErrorHandler;

use \PHPUnit_Framework_TestCase;
use Whoops\RunInterface;
use Whoops\Handler\HandlerInterface;

class WhoopsErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    protected function getMockRunner()
    {
        return $this->getMockBuilder(RunInterface::class)
            ->getMock();
    }

    protected function getMockHandler()
    {
        return $this->getMockBuilder(HandlerInterface::class)
            ->getMock();
    }

    public function testConstructorSetsRunner()
    {
        $given = $expected = $this->getMockRunner();

        $instance = new WhoopsErrorHandler($given, $this->getMockHandler());

        $this->assertAttributeEquals($expected, 'runner', $instance);
    }

    public function testConstructorSetsHandler()
    {
        $given = $expected = $this->getMockHandler();

        $instance = new WhoopsErrorHandler($this->getMockRunner(), $given);

        $this->assertAttributeEquals($expected, 'handler', $instance);
    }

    public function testLoadPushesHandler()
    {
        $handler = $this->getMockHandler();
        $runner  = $this->getMockRunner();

        $runner->expects($this->once())
            ->method('pushHandler')
            ->with($handler)
            ->willReturn($runner);

        $instance = new WhoopsErrorHandler($runner, $handler);

        $instance->load();
    }

    public function testLoadRegistersHandler()
    {
        $handler = $this->getMockHandler();
        $runner  = $this->getMockRunner();

        $runner->expects($this->once())
            ->method('pushHandler')
            ->willReturn($runner);

        $runner->expects($this->once())
            ->method('register');

        $instance = new WhoopsErrorHandler($runner, $handler);

        $instance->load();
    }

    public function testUnloadUnregistersHandler()
    {
        $handler = $this->getMockHandler();
        $runner  = $this->getMockRunner();

        $runner->expects($this->once())
            ->method('unregister');

        $instance = new WhoopsErrorHandler($runner, $handler);

        $instance->unload();
    }
}
