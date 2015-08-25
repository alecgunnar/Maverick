<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Router;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Router
{
    protected $collection;

    public function __construct(RouteCollection $routes)
    {
        $this->collection = $routes;
    }

    public function matchRequest(Request $request)
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->collection, $context);

        try {
            return $matcher->matchRequest($request);
        } catch (ResourceNotFoundException $e) {
            return false;
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }
}