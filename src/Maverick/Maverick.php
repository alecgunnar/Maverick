<?php
/**
 * Maverick
 *
 * @package Maverick
 * @author  Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick;

use Symfony\Component\Config\Loader\LoaderInterface;
use Maverick\Router\Router;
use Maverick\Http\StandardRequest;
use Maverick\Http\StandardResponse;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Maverick\Exception\NoControllerException;
use Maverick\Exception\UndefinedControllerException;
use Maverick\Controller\ControllerInterface;
use Maverick\Exception\InvalidControllerException;

class Maverick
{
    private $config;
    private $router;
    private $request;
    private $response;

    public function __construct(LoaderInterface $loader, Router $router=null, StandardRequest $request=null, StandardResponse $response=null)
    {
        $this->config   = $loader;
        $this->router   = $router;
        $this->request  = $request;
        $this->response = $response;

        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        $this->router->getRoutes()->addCollection((new YamlFileLoader(
            $this->config->getLocator()
        ))->load('routes.yml'));
    }

    public function run()
    {
        $params = $this->router->matchRequest($this->request);

        $this->request->attributes->add($this->filterParams($params));

        $response = $params['_controller']->doAction($this->request);

        if ($response instanceof Response) {
            return $response->send();
        }

        return $this->response->send();
    }

    private function filterParams($params)
    {
        $filtered = [];

        foreach ($params as $key => $value) {
            if ($key[0] != '_') {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}