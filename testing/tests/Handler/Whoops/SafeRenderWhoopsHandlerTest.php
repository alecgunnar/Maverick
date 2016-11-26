<?php

namespace Maverick\Handler\Whoops;

use PHPUnit_Framework_TestCase;
use Exception;
use Whoops\RunInterface;
use Whoops\Handler\Handler;
use Maverick\View\ViewInterface;

class SafeRenderWhoopsHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultViewRenderedIfNoStatusViewsAreGiven()
    {
        $output = 'rendered view content';

        $whoops = $this->getMockWhoops();

        $view = $this->getMockView(true, $output);

        $instance = new SafeRenderWhoopsHandler($view);

        $instance->setRun($whoops);

        ob_start();

        $instance->handle();

        $this->assertEquals($output, ob_get_clean());
    }

    public function testStatusViewRenderedIfOneIsProvided()
    {
        $output = 'not found';

        $code = 404;

        $whoops = $this->getMockWhoops($code);

        $default = $this->getMockView(false, 'default content');
        $view = $this->getMockView(true, $output);

        $instance = new SafeRenderWhoopsHandler($default);

        $instance->setRun($whoops);
        $instance->addView($code, $view);

        ob_start();

        $instance->handle();

        $this->assertEquals($output, ob_get_clean());
    }

    public function testHandleTellsWhoopsToQuit()
    {
        $whoops = $this->getMockWhoops();

        $view = $this->getMockView(true);

        $instance = new SafeRenderWhoopsHandler($view);

        $instance->setRun($whoops);

        ob_start();

        $this->assertEquals(Handler::QUIT, $instance->handle());

        ob_get_clean();
    }

    protected function getMockView(bool $render = false, string $output = 'rendered view')
    {
        $mock = $this->getMockBuilder(ViewInterface::class)
            ->getMock();

        $expect = $render ? $this->once() : $this->never();

        $mock->expects($expect)
            ->method('render')
            ->willReturn($output);

        return $mock;
    }

    protected function getMockException()
    {
        return $this->getMockBuilder(Exception::class)
            ->getMock();
    }

    protected function getMockWhoops(int $code = 0)
    {
        $mock = $this->getMockBuilder(RunInterface::class)
            ->getMock();

        $mock->expects($this->any())
            ->method('sendHttpCode')
            ->willReturn($code);

        return $mock;
    }
}
