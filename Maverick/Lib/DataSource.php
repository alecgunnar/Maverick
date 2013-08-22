<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

interface DataSource {
    /** 
     * Posts to a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function post($params=null);

    /** 
     * Gets a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function get($params=null);

    /** 
     * Updates a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function put($params=null);

    /** 
     * Deletes a resource
     *
     * @param  mixed $params
     * @return mixed
     */
    public function delete($params=null);
}