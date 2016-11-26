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

    public function enable()
    {
        $this->whoops->register();
    }

    public function disable()
    {
        $this->whoops->unregister();
    }
}
