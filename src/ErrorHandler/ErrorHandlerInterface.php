<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */

namespace Maverick\ErrorHandler;

interface ErrorHandlerInterface
{
    /**
     * Does all of the necessary loading of
     * the error handler.
     */
    public function load();
}
