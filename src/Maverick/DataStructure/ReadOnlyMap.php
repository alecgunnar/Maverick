<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

use Maverick\Exception\InvalidTypeException,
    Maverick\Exception\UnavailableMethodException;

class ReadOnlyMap extends Map {
    /**
     * Constructor
     *
     * @throws Maverick\Exception\InvalidTypeException
     * @param  array $data
     */
    public function __construct($data=[]) {
        if(!is_array($data)) {
            throw new InvalidTypeException(__METHOD__, 1, ['array'], $data);
        }

        foreach($data as $key => $value) {
            if($value !== null) {
                $this->data[$key] = $value;
                $this->keys[]     = $key;
            }
        }
    }

    /**
     * Not available since this map is readonly
     *
     * @throws Maverick\Exception\UnavailableMethodException
     */
    public function set($key, $value=null) {
        throw new UnavailableMethodException(__METHOD__);
    }
}