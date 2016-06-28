<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Entity;

use Maverick\Middleware\Queue\MiddlewareQueueTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class RouteEntity implements RouteEntityInterface
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
     * @var mixed
     */
    protected $handler;

    /**
     * @var string
     */
    const SLASH = '/';

    /**
     * @param string[] $methods = []
     * @param string $path = ''
     * @param mixed $handler = null
     */
    public function __construct(array $methods = [], string $path = '', $handler = null)
    {
        $this->methods = $methods;
        $this->handler = $handler;

        $this->setPath($path);
    }

    /**
     * @inheritDoc
     */
    public function withMethods(array $methods): RouteEntityInterface
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
        $this->path = self::SLASH . $this->cleanPath($path);

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
    public function setHandler($handler): RouteEntityInterface
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function withPrefix(string $prefix): RouteEntityInterface
    {
        return $this->setPath($this->cleanPath($prefix) . $this->path);
    }

    /**
     * @param string $path = null
     * @return string
     */
    protected function cleanPath(string $path = null): string
    {
        if (!$path || $path == self::SLASH) {
            return '';
        }

        return trim($path, self::SLASH);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (is_callable($this->handler)) {
            $this->withMiddleware($this->handler);
        }

        return $this->run($request, $response);
    }
}
