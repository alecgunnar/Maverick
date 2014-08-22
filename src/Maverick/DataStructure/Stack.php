<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

class Stack extends ArrayList {
    /**
     * Adds values to the stack
     *
     * @param mixed $value
     */
    public function push($value) {
        $this->add($value);
    }

    /**
     * Gets the next value off of the stack
     *
     * @return mixed
     */
    public function pop() {
        return array_pop($this->data);
    }
}