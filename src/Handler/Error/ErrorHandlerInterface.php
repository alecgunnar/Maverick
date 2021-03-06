<?php

namespace Maverick\Handler\Error;

interface ErrorHandlerInterface
{
    /**
     * Enable this error handler
     */
    public function enable(): void;

    /**
     * Disable this error handler
     */
    public function disable(): void;
}
