<?php

namespace Maverick\Container\Exception;

use Interop\Containter\Exception\ContainerException;

class RetrievalException extends Exception implements ContainerException
{
    /**
     * @param string $name
     * @param string $reason = null
     */
    public function __construct(string $name, string $reason = null)
    {
        $message = sprintf('The service "%s" could not be retrieved from the container. Reason: ', $name, $reason ?? 'Unknown');
        parent::__construct($message);
    }
}
