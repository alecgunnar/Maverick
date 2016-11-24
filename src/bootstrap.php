<?php

namespace Maverick;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Cache\Container as CachedContainer;

/**
 * @param string $root = null
 * @param bool $debug = false
 */
function bootstrap(string $root = null): ContainerInterface
{
    $container = null;

    $root = $root ?? __DIR__;

    /*
     * Try to load the container from the cache
     */

    if (class_exists(CachedContainer::class)) {
        $container = new CachedContainer();
    }

    /*
     * Can't load the container from cache?
     * Build it from the config files
     */

    if (!($container instanceof ContainerInterface)) {
        $container = new ContainerBuilder();
        $container->setParameter('root_dir', $root);

        $loader = new YamlFileLoader($container, new FileLocator($root . '/config'));
        $loader->load('config.yml');
    }

    return $container;
}
