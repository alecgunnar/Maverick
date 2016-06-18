<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Route;

use Maverick\Middleware\Queue\MiddlewareQueueTrait;

class Route implements RouteInterface
{
    use MiddlewareQueueTrait;

    /**
     * @var string[]
     */
    protected $methods;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var callable
     */
    protected $handler;

    /**
     * @param string[] $methods = []
     * @param string $path = ''
     * @param callable $handler = null
     */
    public function __construct(array $methods = [], string $path = '', callable $handler = null)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function setMethods(array $methods): RouteInterface
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path): RouteInterface
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setHandler(callable $handler): RouteInterface
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }
}
