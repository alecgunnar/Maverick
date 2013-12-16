<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

interface DataSource {
    /** 
     * Posts a resource
     */
    public function post();

    /** 
     * Gets a resource
     */
    public function get();

    /** 
     * Updates a resource
     */
    public function put();

    /** 
     * Deletes a resource
     */
    public function delete();

    /**
     * Escapes data for the resource
     *
     * @param  mixed $obj
     * @return mixed
     */
    public function escape($obj);
}