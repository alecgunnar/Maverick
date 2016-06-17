<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Route;

use Maverick\Middleware\Queue\MiddlewareQueueInterface;

interface RouteInterface extends MiddlewareQueueInterface
{
    /**
     * @param string[] $methods
     * @return RouteInterface
     */
    public function setMethods(array $methods): RouteInterface;

    /**
     * @return string[] $methods
     */
    public function getMethods(): array;

    /**
     * @param string $path
     * @return RouteInterface
     */
    public function setPath(string $path): RouteInterface;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param callable $handler
     * @return RouteInterface
     */
    public function setHandler(callable $handler): RouteInterface;

    /**
     * @return callable
     */
    public function getHandler(): callable;
}
