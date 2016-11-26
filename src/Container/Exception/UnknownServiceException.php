<?php

namespace Maverick\Container\Exception;

use Interop\Container\Exception\NotFoundException;
use Exception;

class UnknownServiceException extends Exception implements NotFoundException
{
    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $message = sprintf('The service "%s" was not found in the container.', $name);
        parent::__construct($message);
    }
}
