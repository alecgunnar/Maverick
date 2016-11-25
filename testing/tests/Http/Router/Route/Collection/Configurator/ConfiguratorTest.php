<?php

namespace Maverick\Http\Router\Route\Collection\Configurator;

use PHPUnit_Framework_TestCase;
use Maverick\Http\Router\Route\Collection\Loader\LoaderInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;

class ConfiguratorTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguratorAddsRoutesFromLoaderToCollection()
    {
        $collection = $this->getMockCollection();

        $loader = $this->getMockLoader();
        $loader->expects($this->once())
            ->method('loadRoutes')
            ->with($collection);

        $instance = new Configurator($loader);

        $instance->configure($collection);
    }

    protected function getMockLoader()
    {
        return $this->getMockBuilder(LoaderInterface::class)
            ->getMock();
    }

    protected function getMockCollection()
    {
        return $this->getMockBuilder(CollectionInterface::class)
            ->getMock();
    }
}
