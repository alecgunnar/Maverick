<?php

namespace Maverick\Handler;

interface ErrorHandlerInterface
{
    /**
     * Enable this error handler
     */
    public function enable();

    /**
     * Disable this error handler
     */
    public function disable();
}
