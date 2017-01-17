<?php

namespace Maverick\Handler\Error;

use Whoops\RunInterface;

class WhoopsErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var Whoops\RunInterface
     */
    protected $whoops;

    /**
     * @param RunInterface $whoops
     */
    public function __construct(RunInterface $whoops)
    {
        $this->whoops = $whoops;
    }

    public function enable(): void
    {
        $this->whoops->register();
    }

    public function disable(): void
    {
        $this->whoops->unregister();
    }
}
