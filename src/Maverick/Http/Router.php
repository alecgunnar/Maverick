<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\DependencyManagement\ServiceManager;

class Router {
    /**
     * The request being routed
     *
     * @var Maverick\Http\Request
     */
    protected $request;

    /**
     * The response to be sent
     *
     * @var Maverick\Http\Response
     */
    protected $response;

    /**
     * The service manager
     *
     * @var Maverick\DependencyManagement\ServiceManager
     */
    protected $services;

    /**
     * Has a route been found?
     *
     * @var boolean
     */
    protected $routeFound = false;

    /**
     * Constructor
     *
     * @param Maverick\Http\Request $request
     */
    public function __construct(Request $request, Response $response, ServiceManager $services) {
        $this->request  = $request;
        $this->response = $response;
        $this->services = $services;
    }

    /**
     * Checks a given urn against the current urn
     *
     * Returns an array of matches parameters from the URN
     * or false on failure to match the URN.
     *
     * @param  string $urn
     * @return array | boolean
     */
    private function checkUrn($urn) {
        if($urn === $this->request->getUrn()) {
            return [];
        }

        $args       = [];
        $parts      = explode('/', trim($urn, '/'));
        $parameters = explode('/', trim($this->request->getUrn(), '/'));

        if(count($parts) != count($parameters)) {
            return false;
        }

        foreach($parts as $n => $check) {
            if(preg_match('/{(.+)}/', $check, $checkMatches)) {
                if(preg_match($checkMatches[1], $parameters[$n], $paramMatches)) {
                    $args[] = $paramMatches[1];
                    continue;
                }

                return false;
            }

            if($parameters[$n] != $check) {
                return false;
            }
        }

        return $args;
    }

    /**
     * Attempts to match the specfied request data to the current request
     *
     * @param  string $method
     * @param  string $urn
     * @param  mixed  $controller
     * @param  array  $require=[]
     * @return boolean
     */
    public function match($method, $urn, $controller, $require=[]) {
        $params = [];

        if($this->routeFound === true
            || ($method != '*' && in_array($this->request->getMethod(), explode('|', strtolower($method))) === false)
            || isset($require['https']) && (($require['https'] === true && !$this->request->isHttps()) || ($require['https'] === false && $this->request->isHttps()))
            || ($params = $this->checkUrn($urn)) === false) {
            return false;
        }

        $this->routeFound = true;

        $this->response->setBody($this->services->call($controller, $params) ?: '');

        return true;
    }

    /**
     * Defines a valid route for GET requests
     *
     * @param  string $urn
     * @param  mixed  $controller
     * @param  array  $require=[]
     * @return boolean
     */
    public function get($urn, $controller, $require=[]) {
        return $this->match('get', $urn, $controller, $require);
    }

    /**
     * Defines a valid route for POST requests
     *
     * @param  string $urn
     * @param  mixed  $controller
     * @param  array  $require=[]
     * @return boolean
     */
    public function post($urn, $controller, $require=[]) {
        return $this->match('post', $urn, $controller, $require);
    }

    /**
     * Defines a valid route for PUT requests
     *
     * @param  string $urn
     * @param  mixed  $controller
     * @param  array  $require=[]
     * @return boolean
     */
    public function put($urn, $controller, $require=[]) {
        return $this->match('put', $urn, $controller, $require);
    }

    /**
     * Defines a valid route for DELETE requests
     *
     * @param  string $urn
     * @param  mixed  $controller
     * @param  array  $require=[]
     * @return boolean
     */
    public function delete($urn, $controller, $require=[]) {
        return $this->match('delete', $urn, $controller, $require);
    }

    /**
     * Gets whether or not a route has been found.
     *
     * @codeCoverageIgnore
     * @return boolean
     */
    public function hasRouted() {
        return $this->routeFound;
    }
}