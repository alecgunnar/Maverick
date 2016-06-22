<?php
/**
 * Maverick Framework
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

class Application implements ContainerInterface, MiddlewareQueueInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var ContainerInterface[]
     */
    protected $containers = [];

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
        $this->loadErrorHandler();
        $this->loadMiddleware();
        return $this;
    }

    /**
     * Create the system's container with all of
     * the basic dependencies
     */
    public function loadContainer()
    {
        $builder = new ContainerBuilder();

        $builder->useAutowiring(false);
        $builder->useAnnotations(false);
        $builder->wrapContainer($this);

        $builder->addDefinitions(dirname(__DIR__) . '/app/config/container.php');

        $this->withContainer($builder->build());
    }

    /**
     * Load framework specific middleware
     */
    public function loadMiddleware()
    {
        $this->withMiddleware($this->get('system.middleware.router'))
            ->withMiddleware($this->get('system.middleware.response_sender'));
    }

    /**
     * Loads the error handler found in the container
     */
    public function loadErrorHandler()
    {
        $this->get('system.error_handler');
            ->load();
    }
}
