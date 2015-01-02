<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http\ResponseInstruction;

use Maverick\Http\Response;

interface ResponseInstruction {
    /**
     * A factory method to standardize the creation of
     * response instructions.
     *
     * Default values should be assumed when no arguments
     * are supplied.
     */
    public static function factory();

    /**
     * Modifies the response
     *
     * @param Maverick\Http\Response $response
     */
    public function instruct(Response $response);
}