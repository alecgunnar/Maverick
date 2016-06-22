<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\ErrorHandler;

use Whoops\RunInterface;
use Whoops\Handler\HandlerInterface;

class WhoopsErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var RunInterface
     */
    protected $runner;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @param RunInterface $runner
     */
    public function __construct(RunInterface $runner, HandlerInterface $handler)
    {
        $this->runner  = $runner;
        $this->handler = $handler;
    }

    /**
     * @inheritDocs
     */
    public function load()
    {
        $this->runner->pushHandler($this->handler)
            ->register();
    }

    /**
     * @interitDocs
     */
    public function unload()
    {
        $this->runner->unregister();
    }
}
