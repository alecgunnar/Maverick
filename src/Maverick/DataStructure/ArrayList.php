<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\DataStructure;

class ArrayList extends Iterable {
    /**
     * Constructor
     *
     * @param mixed $data
     */
    public function __construct($data=null) {
        $this->add($data);
    }

    /**
     * Adds item(s) to the list
     *
     * @param mixed $data
     */
    public function add($data) {
        if(is_array($data)) {
            foreach($data as $val) {
                $this->add($val);
            }
        } else {
            if($data !== null) {
                $this->data[] = $data;
            }
        }
    }
}