<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

class Queue extends ArrayList {
    /**
     * Adds values to the queue
     *
     * @param mixed $value
     */
    public function enqueue($value) {
        $this->add($value);
    }

    /**
     * Gets the next value in the queue
     *
     * @return mixed
     */
    public function dequeue() {
        return array_shift($this->data);
    }
}