<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

use Iterator;

class Iterable implements Iterator {
    /**
     * The data contained within this list
     *
     * @var array
     */
    protected $data = [];

    /**
     * The current postition
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Rewinds to the starting position
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * Gets the value at the current position
     */
    public function current() {
        return $this->data[$this->position];
    }

    /**
     * Gets the current key
     *
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * Increments the position
     */
    public function next() {
        $this->position++;
    }

    /**
     * Checks if a key exists
     *
     * @return boolean
     */
    public function valid() {
        return isset($this->data[$this->position]);
    }

    /**
     * Gets the length of the list
     *
     * @return int
     */
    public function getLength() {
        return count($this->data);
    }

    /**
     * Dumps the list
     *
     * @return array
     */
    public function dump() {
        return $this->data;
    }
}