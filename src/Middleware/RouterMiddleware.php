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
     * @var callable
     */
    protected $notFoundHandler;

    /**
     * @var callable
     */
    protected $notAllowedHandler;

    /**
     * @param AbstractRouter $router
     */
    public function __construct(
        AbstractRouter $router,
        callable $notFoundHandler,
        callable $notAllowedHandler
    ) {
        $this->router = $router;
        $this->notFoundHandler = $notFoundHandler;
        $this->notAllowedHandler = $notAllowedHandler;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $result = $this->router->checkRequest($request);
        $params = $this->router->getParams();

        switch ($result) {
            case AbstractRouter::ROUTE_FOUND:
                $handler = $this->router->getMatchedRoute();
                break;

            case AbstractRouter::ROUTE_NOT_FOUND:
                $handler = $this->notFoundHandler;
                break;

            case AbstractRouter::ROUTE_NOT_ALLOWED:
                $params  = $this->router->getAllowedMethods();
                $handler = $this->notAllowedHandler;
                break;
        }

        $response = $handler($request, $response, $params);

        return $next($request, $response);
    }
}
