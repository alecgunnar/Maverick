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
    protected $notFoundController;

    /**
     * @var callable
     */
    protected $notAllowedController;

    /**
     * @var string
     */
    const ALLOWED_METHODS = 'ALLOWED_METHODS';

    /**
     * @param AbstractRouter $router
     * @param callable $notFoundController
     * @param callable $notAllowedController
     */
    public function __construct(
        AbstractRouter $router,
        callable $notFoundController,
        callable $notAllowedController
    ) {
        $this->router = $router;
        $this->notFoundController = $notFoundController;
        $this->notAllowedController = $notAllowedController;
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
                break;

            case AbstractRouter::ROUTE_NOT_FOUND:
                $controller = $this->notFoundController;
                break;

            case AbstractRouter::ROUTE_NOT_ALLOWED:
                $methods = $this->router->getAllowedMethods();
                $controller = $this->notAllowedController;
                break;
        }

        if (isset($route)) {
            foreach ($params as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            $controller = $route;
        } else if (isset($methods)) {
            $request = $request->withAttribute(self::ALLOWED_METHODS, $methods);
        }

        return $controller($request, $response, $next);
    }
}
