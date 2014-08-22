<?php

/**
 * Maverick Framework
 *
 * (c) Alec Carpenter <gunnar94@me.com>
 */

namespace Maverick\Http\Response\Instruction;

use Maverick\Http\Response;

interface InstructionInterface {
    /**
     * Modifies the response
     *
     * @param Maverick\Http\Response $response
     */
    public function instruct(Response $response);
}