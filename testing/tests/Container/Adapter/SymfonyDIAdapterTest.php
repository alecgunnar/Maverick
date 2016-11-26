<?php

namespace Maverick\Container\Adapter;

use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use InvalidArgumentException;
use Exception;

class SymfonyDIAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testServiceCanBeGottenFromAdaptedContainer()
    {
        $name = 'service_name';
        $service = new \stdClass();

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->willReturn($service);

        $instance = new SymfonyDIAdapter($container);

        $this->assertSame($service, $instance->get($name));
    }

    public function testParameterCanBeGottenFromAdaptedContainer()
    {
        $name = 'parameter_name';
        $parameter = new \stdClass();

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->will($this->throwException(new ServiceNotFoundException($name)));

        $container->expects($this->once())
            ->method('getParameter')
            ->with($name)
            ->willReturn($parameter);

        $instance = new SymfonyDIAdapter($container);

        $this->assertSame($parameter, $instance->get($name));
    }

    public function testServicesAreGottenBeforeParametersWithSameName()
    {
        $name = 'test_name';
        $service = new \stdClass();
        $parameter = new \stdClass();

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->willReturn($service);

        $container->expects($this->never())
            ->method('getParameter')
            ->with($name)
            ->willReturn($parameter);

        $instance = new SymfonyDIAdapter($container);

        $this->assertSame($service, $instance->get($name));
    }

    public function testExceptionThrownIfServiceAndParameterDoNotExist()
    {
        $this->expectException(NotFoundException::class);

        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->will($this->throwException(new ServiceNotFoundException($name)));

        $container->expects($this->once())
            ->method('getParameter')
            ->with($name)
            ->will($this->throwException(new InvalidArgumentException()));

        $instance = new SymfonyDIAdapter($container);

        $instance->get($name);
    }

    public function testExceptionThrownIfServiceCannotBeLoaded()
    {
        $this->expectException(ContainerException::class);

        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->once())
            ->method('get')
            ->with($name)
            ->will($this->throwException(new Exception()));

        $container->expects($this->never())
            ->method('getParameter')
            ->with($name);

        $instance = new SymfonyDIAdapter($container);

        $instance->get($name);
    }

    public function testHasReturnsTrueIfServiceExists()
    {
        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->any())
            ->method('has')
            ->with($name)
            ->willReturn(true);

        $container->expects($this->any())
            ->method('hasParameter')
            ->with($name)
            ->willReturn(false);

        $instance = new SymfonyDIAdapter($container);

        $this->assertTrue($instance->has($name));
    }

    public function testHasReturnsTrueIfParameterExists()
    {
        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->any())
            ->method('has')
            ->with($name)
            ->willReturn(false);

        $container->expects($this->any())
            ->method('hasParameter')
            ->with($name)
            ->willReturn(true);

        $instance = new SymfonyDIAdapter($container);

        $this->assertTrue($instance->has($name));
    }

    public function testHasRetutnsTrueIfBothExist()
    {
        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->any())
            ->method('has')
            ->with($name)
            ->willReturn(true);

        $container->expects($this->any())
            ->method('hasParameter')
            ->with($name)
            ->willReturn(true);

        $instance = new SymfonyDIAdapter($container);

        $this->assertTrue($instance->has($name));
    }

    public function testHasReturnsFalseIfBothDoNotExist()
    {
        $name = 'test_name';

        $container = $this->getMockContainer();

        $container->expects($this->any())
            ->method('has')
            ->with($name)
            ->willReturn(false);

        $container->expects($this->any())
            ->method('hasParameter')
            ->with($name)
            ->willReturn(false);

        $instance = new SymfonyDIAdapter($container);

        $this->assertFalse($instance->has($name));
    }

    protected function getMockContainer()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
    }
}
