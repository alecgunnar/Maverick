<?php

namespace Maverick\Handler\Whoops;

use Whoops\Handler\Handler;
use Maverick\Http\Exception\HttpExceptionInterface;

class HttpExceptionWhoopsHandler extends Handler
{
    public function handle()
    {
        $exception = $this->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $this->getRun()->sendHttpCode(
                $exception->getStatusCode()
            );
        }
    }
}
