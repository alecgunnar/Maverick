<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick;

use Acclimate\Container\CompositeContainer;
use Interop\Container\ContainerInterface;
use DI\ContainerBuilder;
use Maverick\Middleware\MiddlewareAwareTrait;
use Maverick\Router\FastRouteRouter;
use Maverick\Router\Collection\RouteCollection;
use Maverick\Router\Loader\FileSystemLoader;
use Maverick\Handler\NotFoundHandler;
use Maverick\Handler\NotAllowedHandler;

class Application extends CompositeContainer
{
    use MiddlewareAwareTrait;

    /**
     * @param ContainerInterface[] $containers
     */
    public function __construct(array $containers)
    {
        parent::__construct($containers);

        $this->initialize();
    }

    /**
     * Perform generic setup tasks
     */
    protected function initialize()
    {
        $this->loadContainer();
    }

    /**
     * Create the system's container with all of
     * the basic dependencies
     */
    protected function loadContainer()
    {
        $builder = new ContainerBuilder();

        $builder->useAutowiring(false);
        $builder->useAnnotations(false);
        $builder->wrapContainer($this);

        $builder->setDefinitions([
            'system.route_collection' => function() {
                return new Collection();
            },
            'system.route_loader' => function($c) {
                return new FileSystemLoader($c->get('system.route_collection'));
            },
            'system.router' => function($c) {
                return new FastRouteRouter($c->get('system.route_collection'));
            },
            'system.handler.not_found' => function() {
                return new NotFoundHandler();
            },
            'system.handler.not_allowed' => function() {
                return new NotAllowedHandler();
            },
        ]);

        $this->addContainer($builder->build());
    }
}
