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
use Maverick\Resolver\ResolverInterface;

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
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @param AbstractRouter $router
     */
    public function __construct(
        AbstractRouter $router,
        callable $notFoundHandler,
        callable $notAllowedHandler,
        ResolverInterface $resolver
    ) {
        $this->router = $router;
        $this->notFoundHandler = $notFoundHandler;
        $this->notAllowedHandler = $notAllowedHandler;
        $this->resolver = $resolver;
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
                $handler = $this->router->getMatchedRoute()
                    ->getHandler();

                if (!is_callable($handler)) {
                    $handler = $this->resolver->resolve($handler);
                }

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
