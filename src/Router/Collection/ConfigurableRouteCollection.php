<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router\Collection;

use Maverick\Router\Collection\Factory\RouteCollectionFactory;
use Maverick\Router\Factory\Entity\RouteEntityFactory;

class ConfigurableRouteCollection extends RouteCollection
{
    /**
     * @var RouteCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RouteEntityFactory
     */
    protected $entityFactory;

    /**
     * @param RouteCollectionFactory $collection
     * @param RouteEntityFactory $entity
     */
    public function __construct(
        RouteCollectionFactory $collection,
        RouteEntityFactory $entity
    ) {
        $this->collectionFactory = $collection;
        $this->entityFactory = $entity;
    }
}
