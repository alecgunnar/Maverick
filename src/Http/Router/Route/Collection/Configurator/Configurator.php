<?php

namespace Maverick\Http\Router\Route\Collection\Configurator;

use Maverick\Http\Router\Route\Collection\Loader\LoaderInterface;
use Maverick\Http\Router\Route\Collection\CollectionInterface;

class Configurator implements ConfiguratorInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function configure(CollectionInterface $collection)
    {
        $this->loader->loadRoutes($collection);
    }
}
