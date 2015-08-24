<?php
/**
 * Maverick
 *
 * @author Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as DependencyInjectionYamlLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader as RouterYamlLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Maverick\Exception\NoControllerException;
use Maverick\Exception\UndefinedControllerException;
use Maverick\Controller\ControllerInterface;
use Maverick\Exception\InvalidControllerException;

class Maverick
{
    protected $config;
    protected $container;
    protected $routes;
    protected $request;
    protected $response;

    public function __construct(LoaderInterface $loader, Request $request=null, Response $response=null)
    {
        $this->config = $loader;

        $this->loadContainer();
        $this->loadRoutes();

        $this->request  = $request  ?: Request::createFromGlobals();
        $this->response = $response ?: Response::create();
    }

    private function loadContainer()
    {
        $this->container = new ContainerBuilder();

        $this->loadFrameworkServices();
        $this->loadApplicationServices();
    }

    public function loadFrameworkServices()
    {
        $frameworkConfig = ROOT_PATH . DIRECTORY_SEPARATOR . 'config';

        (new DependencyInjectionYamlLoader(
            $this->container,
            new FileLocator($frameworkConfig)
        ))->load('services.yml');
    }

    public function loadApplicationServices()
    {
        (new DependencyInjectionYamlLoader(
            $this->container,
            $this->config->getLocator()
        ))->load('services.yml');
    }

    private function loadRoutes()
    {
        $this->routes = (new RouterYamlLoader(
            $this->config->getLocator()
        ))->load('routes.yml');
    }

    public function run()
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);

        $matcher = new UrlMatcher($this->routes, $context);
        $params  = $matcher->matchRequest($this->request);

        if (!isset($params['_controller'])) {
            throw new NoControllerException(sprintf('No controller provided for route %s.', $params['_route']));
        }

        if (!$this->container->has($params['_controller'])) {
            throw new UndefinedControllerException(sprintf('The controller %s is not defined.', $params['_controller']));
        }

        $controller = $this->container->get($params['_controller']);

        if (!($controller instanceof ControllerInterface)) {
            throw new InvalidControllerException(sprintf('The controller %s is not properly implemented.', $params['_controller']));
        }

        $response = call_user_func_array([$controller, 'doAction'], $this->filterParams($params));

        if ($response instanceof Response) {
            return $response->send();
        }

        return $this->response->setContent($response)->send();
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

    public function getContainer()
    {
        return $this->container;
    }

    public function getRoutes()
    {
        return $this->routes;
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