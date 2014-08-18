<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DependencyManagement;

use Maverick\DataStructure\Map,
    Maverick\Exception\InvalidArgumentException,
    Maverick\Exception\UnknownValueException,
    Maverick\Exception\InvalidOperationException,
    Maverick\Exception\DuplicateValueException,
    Maverick\Exception\InvalidValueException;

class ServiceManager {
    /**
     * A map of services currently registered with this manager
     *
     * @var Maverick\DataStructure\Map
     */
    private $services;

    /**
     * A map of services which have been instantiated
     *
     * @var Maverick\DataStructure\Map
     */
    private $objects;

    /**
     * Constructor
     */
    public function __construct() {
        $this->services = new Map();
        $this->objects  = new Map();
    }

    /**
     * Registers a service
     *
     * @throws Maverick\Exception\InvalidArgumentException
     * @param  string   $name
     * @param  callable $callback
     */
    public function register($name, $callback) {
        if($this->services->has($name)) {
            throw new DuplicateValueException('A service already exists with the name ' . $name . '. You must call ' . __CLASS__ . '::replace to redefine the service.');
        }

        if(!is_callable($callback)) {
            throw new InvalidArgumentException(__METHOD__, 2, ['callable']);
        }

        $this->services->set($name, $callback);

        if($this->objects->get($name)) {
            $this->objects->set($name, null);
        }
    }

    /**
     * Replaces a service's callback with another
     *
     * @param string   $name
     * @param callable $callback
     */
    public function replace($name, $callable) {
        if(!$this->services->has($name)) {
            throw new UnknownValueException('Unknown service ' . $name);
        }

        if($this->objects->has($name)) {
            throw new InvalidOperationException('Cannot replace service ' . $name . ' because it has already been instantiated.');
        }

        $current = $this->services->get($name);
        $type    = get_class($current($this));

        if(!($returned = $callable($this) instanceof $type)) {
            throw new InvalidValueException('The callable supplied to method ' . __METHOD__ . ' must return type ' . $type . '. Type ' . gettype($returned) . ' was returned.');
        }

        $this->services->set($name, $callable);
    }

    /**
     * Gets the known instance of a service
     * creates an instance otherwise
     *
     * @throws Maverick\Exception\UnknownValueException
     * @param  string $name
     * @return mixed
     */
    public function get($name) {
        if(!$this->objects->has($name)) {
            $this->objects->set($name, $this->getNew($name));
        }

        return $this->objects->get($name);
    }

    /**
     * Gets a new instance of a service
     *
     * This WILL return a value DIFFERENT from the one stored in $objects
     *
     * @throws Maverick\Exception\UnknownValueException
     * @param  string $name
     * @return mixed
     */
    public function getNew($name) {
        if(!$this->services->has($name)) {
            throw new UnknownValueException('Unknown service ' . $name);
        }

        $callable = $this->services->get($name);

        return $callable($this);
    }

    /**
     * Call a method of a service
     *
     * @throws Maverick\Exception\InvalidArgumentException
     * @throws Maverick\Exception\InvalidValueException
     * @param  string $call
     * @param  array  $args=[]
     * @return mixed
     */
    public function call($call, array $args=[]) {
        if(!is_string($call)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['string']);
        }

        $exp = explode('->', $call);

        if(count($exp) !== 2) {
            throw new InvalidValueException('A valid service method call must be formatted as: "service.name->methodName"');
        }

        return call_user_func_array(array($this->get($exp[0]), $exp[1]), $args);
    }

    /**
     * Returns the map of services
     *
     * @return Maverick\DataStructure\Map
     */
    public function getServices() {
        return $this->services;
    }
}