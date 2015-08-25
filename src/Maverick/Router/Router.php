<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Router;

use Symfony\Component\Routing\RouteCollection;
use Maverick\Collection\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Maverick\Router\Exception\NoControllerException;
use Maverick\Router\Exception\UndefinedControllerException;

class Router
{
    protected $routes;
    protected $controllers;

    public function __construct(RouteCollection $routes, ControllerCollection $controllers)
    {
        $this->routes      = $routes;
        $this->controllers = $controllers;
    }

    public function matchRequest(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        $params = $matcher->matchRequest($request);

        if (!isset($params['_controller'])) {
            throw new NoControllerException(sprintf('The route %s does not have controller assigned to it.', $params['_route']));
        }

        if(!($controller = $this->controllers->get($params['_controller']))) {
            throw new UndefinedControllerException(sprintf('The controller %s assigned to route %s does not exist.', $params['_controller'], $params['_route']));
        }

        $params['_controller'] = $controller;

        return $params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getControllers()
    {
        return $this->controllers;
    }
}