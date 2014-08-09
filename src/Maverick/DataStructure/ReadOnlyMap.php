<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

use Maverick\Exception\InvalidArgumentException,
    Maverick\Exception\UnavailableMethodException;

class ReadOnlyMap extends Map {
    /**
     * Constructor
     *
     * @throws Maverick\Exception\InvalidArgumentException
     * @param  array $data
     */
    public function __construct($data=[]) {
        if(!is_array($data)) {
            throw new InvalidArgumentException(__METHOD__, 1, ['array']);
        }

        foreach($data as $key => $value) {
            if($value !== null) {
                $this->data[$key] = $value;
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