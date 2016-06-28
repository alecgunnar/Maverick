<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router\Entity;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;

interface RouteEntityInterface extends MiddlewareQueueInterface
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
     * @param mixed $handler
     * @return RouteEntityInterface
     */
    public function setHandler($handler): RouteEntityInterface;

    /**
     * @return mixed
     */
    public function getHandler();

    /**
     * @param string $prefix
     * @return RouteEntityInterface
     */
    public function withPrefix(string $prefix): RouteEntityInterface;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
