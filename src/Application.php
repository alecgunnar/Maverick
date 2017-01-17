<?php

namespace Maverick;

use Maverick\Handler\Error\ErrorHandlerInterface;
use Maverick\Http\Router\RouterInterface;
use Maverick\Http\Router\Route\RouteInterface;
use Maverick\Http\Exception\NotFoundException;
use Maverick\Http\Exception\NotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Container\ContainerInterface;
use UnexpectedValueException;

class Application
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * If the last argument, the error handler is
     * provided, it will be automatically enabled.
     *
     * @param RouterInterface $router
     * @param ContainerInterface $container
     * @param ErrorHandlerInterface $errorHandler = null
     */
    public function __construct(RouterInterface $router, ContainerInterface $container, ErrorHandlerInterface $errorHandler = null)
    {
        $this->router = $router;
        $this->container = $container;

        if ($errorHandler) {
            $errorHandler->enable();
        }
    }

    /**
     * Given a request, this method will determine
     * which route, if any, matches the request. If a
     * matching route is found, the action will be
     * loaded from the container and called being
     * passed the provided request.
     *
     * @param ServerRequestInterface $request
     *
     * @throws HttpException
     * @throws NotFoundException
     * @throws NotAllowedException
     * @throws UnexpectedValueException
     *
     * @return ResponseInterface $response
     */
    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $status = $this->router->processRequest($request);

        switch ($status) {
            case RouterInterface::STATUS_NOT_FOUND:
                throw new NotFoundException($request);
            case RouterInterface::STATUS_NOT_ALLOWED:
                throw new NotAllowedException($request);
            case RouterInterface::STATUS_FOUND:
                $route = $this->router->getRoute();
        }

        $callable = $this->container->get($route->getService());
        $response = $callable($request);

        if (!($response instanceof ResponseInterface)) {
            $msg = sprintf('Route action did not return an instance of %s.', ResponseInterface::class);
            throw new UnexpectedValueException($msg);
        }

        return $response;
    }

    /**
     * Sends the response back to the client
     *
     * @param ResponseInterface $response
     */
    public function sendResponse(ResponseInterface $response): void
    {
        if (!headers_sent()) {
            $header = sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase());
            header($header);

            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    $header = sprintf('%s: %s', $name, $value);
                    header($header, false);
                }
            }
        }

        echo (string) $response->getBody();
    }
}
