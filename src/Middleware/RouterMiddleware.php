<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Router\AbstractRouter;

class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var AbstractRouter
     */
    protected $router;

    /**
     * @param AbstractRouter $router
     */
    public function __construct(AbstractRouter $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $handler = $this->router->checkRequest($request);

        foreach ($this->router->getParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $next(
            $request,
            $handler($request, $response)
        );
    }
}
