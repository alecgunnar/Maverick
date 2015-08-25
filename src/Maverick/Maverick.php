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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Maverick\Exception\NoControllerException;
use Maverick\Exception\UndefinedControllerException;
use Maverick\Controller\ControllerInterface;
use Maverick\Exception\InvalidControllerException;

class Maverick
{
    protected $config;
    protected $router;
    protected $request;
    protected $response;

    public function __construct(LoaderInterface $loader, Router $router=null, Request $request=null, Response $response=null)
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
        if (!($params = $this->router->matchRequest($this->request))) {
            $params = [
                '_controller' => $this->router->getControllers()->get('maverick.controller.not_found')
            ];
        }

        $response = call_user_func_array([$params['_controller'], 'doAction'], $this->filterParams($params));

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