<?php
/**
 * Maverick Container
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\Router;

use Psr\Http\Message\ServerRequestInterface;
use Maverick\Middleware\Queue\MiddlewareQueueInterface;

interface RouterInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return MiddlewareQueueInterface
     */
    public function handleRequest(ServerRequestInterface $request): MiddlewareQueueInterface;
}
