<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Entity;

use Maverick\Middleware\MiddlewareInterface;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;

interface RouteEntityInterface extends MiddlewareInterface, MiddlewareQueueInterface
{
    /**
     * @param string[] $methods
     * @return RouteEntityInterface
     */
    public function withMethods(array $methods): RouteEntityInterface;

    /**
     * @return string[] $methods
     */
    public function getMethods(): array;

    /**
     * @param string $path
     * @return RouteEntityInterface
     */
    public function setPath(string $path): RouteEntityInterface;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param callable $handler
     * @return RouteEntityInterface
     */
    public function setHandler(callable $handler): RouteEntityInterface;

    /**
     * @return callable
     */
    public function getHandler(): callable;
}
