<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Router;

use Psr\Http\Message\ServerRequestInterface;
use Maverick\Router\Entity\RouteEntityInterface;

abstract class AbstractRouter
{
    /**
     * @var RouteEntityInterface
     */
    protected $matched;

    /**
     * @var string[]
     */
    protected $params = [];

    /**
     * @var string[]
     */
    protected $methods = [];

    /**
     * @var int
     */
    const ROUTE_FOUND = 200;

    /**
     * @var int
     */
    const ROUTE_NOT_FOUND = 404;

    /**
     * @var int
     */
    const ROUTE_NOT_ALLOWED = 405;

    /**
     * @return RouteEntityInterface
     */
    public function getMatchedRoute(): RouteEntityInterface
    {
        return $this->matched;
    }

    /**
     * @return string[]
     */
    public function getParams(): array
    {
        return (array) $this->params;
    }

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return (array) $this->methods;
    }

    /**
     * @param ServerRequestInterface $request
     * @return int
     */
    abstract public function checkRequest(ServerRequestInterface $request): int;
}
