<?php

namespace Maverick\Container;

use Interop\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

class SymfonyContainerAdapter implements ContainerInterface
{
    /**
     * @param SymfonyContainerInterface
     */
    protected $container;

    public function __construct(SymfonyContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }

        if ($this->container->hasParameter($name)) {
            return $this->container->getParameter($name);
        }

        throw \Exception('Service/parameter does not exist');
    }

    public function has($name)
    {
        return $this->container->has($name) || $this->container->hasParameter($name);
    }
}
