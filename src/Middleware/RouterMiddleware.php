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
     * @var string
     */
    const ALLOWED_METHODS = 'ALLOWED_METHODS';

    /**
     * @param AbstractRouter $router
     * @param callable $notFoundHandler
     * @param callable $notAllowedHandler
     * @param ResolverInterface $resolver
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
                $route   = $this->router->getMatchedRoute();
                $handler = $this->resolver->resolve($route->getHandler());
                break;

            case AbstractRouter::ROUTE_NOT_FOUND:
                $handler = $this->notFoundHandler;
                break;

            case AbstractRouter::ROUTE_NOT_ALLOWED:
                $methods = $this->router->getAllowedMethods();
                $handler = $this->notAllowedHandler;
                break;
        }

        if (isset($route)) {
            foreach ($params as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            $route->setHandler($handler);

            $handler = $route;
        } else if (isset($methods)) {
            $request = $request->withAttribute(self::ALLOWED_METHODS, $methods);
        }

        $response = $handler($request, $response);

        return $next($request, $response);
    }
}
