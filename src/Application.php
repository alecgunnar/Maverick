<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick;

use Interop\Container\ContainerInterface;
use DI\ContainerBuilder;
use Maverick\Container\Exception\NotFoundException;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;
use Maverick\Middleware\Queue\MiddlewareQueueTrait;
use Maverick\Middleware\RouterMiddleware;
use Maverick\Router\FastRouteRouter;
use Maverick\Router\Collection\FastRouteCollection;
use Maverick\Router\Loader\FileSystemLoader;
use Maverick\Handler\NotFoundHandler;
use Maverick\Handler\NotAllowedHandler;

class Application implements ContainerInterface, MiddlewareQueueInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var ContainerInterface[]
     */
    protected $containers = [];

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var int
     */
    protected $foundInContainer;

    /**
     * Add a new container
     *
     * @param ContainerInterface $container
     * @return Application
     */
    public function withContainer(ContainerInterface $container): Application
    {
        $this->containers[] = $container;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        foreach ($this->containers as $index => $container) {
            if ($container->has($id)) {
                $this->foundInContainer = $index;
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException('The service ' . $id . ' does not exist.');
        }

        return $this->containers[$this->foundInContainer]
            ->get($id);
    }

    /**
     * Perform generic setup tasks
     *
     * @return Application
     */
    public function initialize(): Application
    {
        $this->loadContainer();
        $this->loadMiddleware();

        $this->initialized = true;

        return $this;
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

        $builder->addDefinitions([
            'system.route_collection' => function() {
                return new FastRouteCollection();
            },
            'system.route_loader' => function($c) {
                return new RouteLoader($c->get('system.route_collection'));
            },
            'system.router' => function($c) {
                $instance = new FastRouteRouter($c->get('system.fast_route.dispatcher'));

                return $instance->setNotFoundHandler($c->get('system.handler.not_found'))
                    ->setNotAllowedHandler($c->get('system.handler.not_allowed'));
            },
            'system.fast_route.dispatcher' => function($c) {
                return \FastRoute\simpleDispatcher(
                    $c->get('system.route_collection'),
                    $c->get('system.fast_route.options')
                );
            },
            'system.fast_route.options' => function() {
                return [];
            },
            'system.handler.not_found' => function() {
                return new NotFoundHandler();
            },
            'system.handler.not_allowed' => function() {
                return new NotAllowedHandler();
            },
            'system.middleware.router' => function($c) {
                return new RouterMiddleware($c->get('system.router'));
            }
        ]);

        $this->withContainer($builder->build());
    }

    /**
     * Load framework specific middleware
     */
    protected function loadMiddleware()
    {
        $this->withMiddleware($this->get('system.middleware.router'));
    }
}
