<?php

namespace Maverick;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\FileLocator;
use Interop\Container\ContainerInterface;
use Acclimate\Container\ContainerAcclimator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use CachedContainer;

/**
 * @param string $root = null
 * @param bool $debug = false
 */
function bootstrap(string $root = null, bool $debug = false): ContainerInterface
{
    $container = null;

    $root = $root ?? __DIR__;

    /*
     * Try to load the container from the cache
     */

    if (!$debug && class_exists(CachedContainer::class)) {
        $container = new CachedContainer();
    }

    /*
     * Can't load the container from cache?
     * Build it from the config files
     */

    if (!($container instanceof Container)) {
        $container = new ContainerBuilder();
        $container->setParameter('root_dir', $root);
        $container->setParameter('is_debug', $debug);

        $loader = new YamlFileLoader($container, new FileLocator($root . '/config'));
        $loader->load('config.yml');
    }

    return $container->get('container');
}
