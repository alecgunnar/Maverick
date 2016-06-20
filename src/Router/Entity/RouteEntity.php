<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Entity;

use Maverick\Middleware\Queue\MiddlewareQueueTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class RouteEntity implements RouteEntityInterface
{
    use MiddlewareQueueTrait {
        __invoke as runMiddlewares;
    }

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
    public function setMethods(array $methods): RouteEntityInterface
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
    public function setPath(string $path): RouteEntityInterface
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
    public function setHandler(callable $handler): RouteEntityInterface
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

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface
    {
        if (is_callable($this->handler)) {
            $this->withMiddleware($this->handler);
        }

        $response = $this->runMiddlewares($request, $response);

        if (is_callable($next)) {
            $response = $next($request, $response);
        }

        return $response;
    }
}
