<?php

namespace Maverick\Container\Cacher;

use InvalidArgumentException;

abstract class ContainerCacher
{
    /**
     * @param mixed $container
     *
     * @throws InvalidArgumentException
     */
    final public function cacheContainer($container);

    /**
     * Return the class name of the container
     * type which can be cached by extensions
     * of this abstract class.
     *
     * @return string
     */
    abstract public function getCacheableType(): string;

    /**
     * @param mixed $container
     */
    abstract protected function doCaching($container);
}
