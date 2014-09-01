<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http;

use Maverick\Application,
    Maverick\DependencyManagement\ServiceManager,
    Maverick\Http\Response\Instruction\InstructionInterface,
    Maverick\DataStructure\Map,
    Maverick\Exception\InvalidTypeException,
    Maverick\Exception\UnknownValueException,
    Maverick\Exception\InvalidValueException;

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
     * All of the named routes
     *
     * @var Maverick\DataStructure\Map
     */
    protected $named;

    /**
     * The controller for the current request
     *
     * @var array
     */
    protected $controller;

    /**
     * Has a route been found?
     *
     * @var boolean
     */
    protected $routeFound = false;

    /**
     * Whether or not the request has been routed
     *
     * @var boolean
     */
    protected $hasRouted = false;

    /**
     * Constructor
     *
     * @param Maverick\Http\Request $request
     */
    public function __construct(Request $request, Response $response, ServiceManager $services) {
        $this->request  = $request;
        $this->response = $response;
        $this->services = $services;
        $this->named    = new Map();
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
     * @param  array  $params=[]
     * @return boolean
     */
    public function match($method, $urn, $controller, $params=[]) {
        $args = [];

        if(isset($params['name'])) {
            $this->named->set($params['name'], $urn);
        }

        if($this->routeFound === true
            || (isset($params['env']) && Application::debugCompare('!=', $params['env']))
            || ($method != '*' && in_array($this->request->getMethod(), explode('|', strtolower($method))) === false)
            || isset($params['https']) && (($params['https'] === true && !$this->request->isHttps()) || ($params['https'] === false && $this->request->isHttps()))
            || ($args = $this->checkUrn($urn)) === false) {
            return false;
        }

        $this->routeFound = true;
        $this->controller = [$controller, $args];

        return true;
    }

    /**
     * Handles the route which has been matched
     *
     * @return boolean
     */
    public function doRoute() {
        if(!$this->routeFound) {
            return false;
        }

        if(!$this->hasRouted) {
            $this->hasRouted = true;

            $this->handleController();
        }

        return true;
    }

    /**
     * Handles calling the controller and dealing with its return value
     */
    private function handleController() {
        $return = $this->services->call($this->controller[0], $this->controller[1]);

        if($return instanceof InstructionInterface) {
            $return->instruct($this->response);
            return;
        }

        $this->response->setBody($return ?: '');
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
     * Generates a URN
     *
     * @throws Maverick\Exception\InvalidTypeException
     * @throws Maverick\Exception\InvalidValueException
     * @throws Maverick\Exception\UnknownValueException
     * @param  string $name
     * @param  array  $params=[]
     * @return string
     */
    public function generateUrn($name, array $params=[]) {
        if(!is_string($name)) {
            throw new InvalidTypeException(__METHOD__, 1, ['string'], $name);
        }

        if(!$this->named->has($name)) {
            throw new UnknownValueException('Unknown named route: ' . $name);
        }

        $exp = explode('/', trim($this->named->get($name), '/'));
        $urn = '';

        foreach($exp as $part) {
            $value = $part;

            if($value[0] == '{') {
                $regex = trim($part, '{}');
                $value = array_shift($params);

                if($value === null) {
                    throw new InvalidValueException('Not enough params have been supplied to ' . __METHOD__ . ' to generate: ' . $name);
                }

                if(!preg_match($regex, $value)) {
                    throw new InvalidValueException($value . ' does not satisfy ' . $regex);
                }
            }

            $urn .= '/' . $value;
        }

        return $urn;
    }

    /**
     * Gets the named routes
     *
     * @return Maverick\DataStructure\Map
     */
    public function getNamedRoutes() {
        return $this->named;
    }

    /**
     * Gets whether or not a route has been found.
     *
     * @codeCoverageIgnore
     * @return boolean
     */
    public function routeFound() {
        return $this->routeFound;
    }

    /**
     * Gets whether or not the request has been routed.
     *
     * @codeCoverageIgnore
     * @return boolean
     */
    public function hasRouted() {
        return $this->hasRouted;
    }
}