<?php

namespace Maverick\Http\Router\Route\Collection\Loader;

use Maverick\Http\Router\Route\Collection\CollectionInterface;

interface LoaderInterface
{
    /**
     * Loads the known routes into the given collection
     *
     * @param CollectionInterface $collection
     */
    public function loadRoutes(CollectionInterface $collection);
}
