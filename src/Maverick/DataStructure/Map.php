<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

use Maverick\Exception\InvalidTypeException;

class Map extends Iterable {
    /**
     * A list of keys
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Constructor
     *
     * @param array $data=[]
     */
    public function __construct(array $data=[]) {
        if(count($data)) {
            $this->set($data);
        }
    }

    /**
     * Sets the data to the map
     *
     * Will not keep values which are null
     *
     * @throws Maverick\Exception\InvalidTypeException
     * @param  string $key
     * @param  mixed  $value=null
     */
    public function set($key, $value=null) {
        if(is_array($key)) {
            foreach($key as $k => $v) {
                $this->set($k, $v);
            }
        } elseif(is_string($key)) {
            if($value !== null) {
                $this->data[$key] = $value;
                $this->keys[]     = $key;
            } else {
                if(isset($this->data[$key])) {
                    unset($this->data[$key]);
                }
            }
        } else {
            throw new InvalidTypeException(__METHOD__, 1, ['string', 'array'], $key);
        }
    }

    /**
     * Test to see if a key exists
     *
     * @param  string $key
     * @return boolean
     */
    public function has($key) {
        return isset($this->data[$key]);
    }

    /**
     * Returns the value associated with a key
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key) {
        if(!is_string($key)) {
            throw new InvalidTypeException(__METHOD__, 1, ['string'], $key);
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Determines if the current key exists
     *
     * @return boolean
     */
    public function valid() {
        if(isset($this->keys[$this->position])) {
            return true;
        }

        return false;
    }

    /**
     * Gets the current value at the current position
     *
     * @return mixed
     */
    public function current() {
        return $this->data[$this->keys[$this->position]];
    }

    /**
     * Gets the key
     *
     * @return string
     */
    public function key() {
        return $this->keys[$this->position];
    }
}