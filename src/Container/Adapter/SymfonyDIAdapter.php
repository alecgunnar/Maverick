<?php

namespace Maverick\Container\Adapter;

use Interop\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Maverick\Container\Exception\RetrievalException;
use Maverick\Container\Exception\UnknownServiceException;
use InvalidArgumentException;
use Exception;

class SymfonyDIAdapter implements ContainerInterface
{
    /**
     * @var SymfonyContainerInterface
     */
    protected $container;

    /**
     * @param SymfonyContainerInterface $container
     */
    public function __construct(SymfonyContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get($name)
    {
        try {
            try {
                return $this->container->get($name);
            } catch (ServiceNotFoundException $exception) {
                return $this->container->getParameter($name);
            }
        } catch (InvalidArgumentException $exception) {
            throw new UnknownServiceException($name);
        } catch (Exception $exception) {
            throw new RetrievalException($name, $exception->getMessage());
        }
    }

    public function has($name)
    {
        return $this->container->has($name) || $this->container->hasParameter($name);
    }
}
