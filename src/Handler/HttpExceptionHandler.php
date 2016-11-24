<?php

namespace Maverick\Handler;

use Whoops\Handler\Handler;
use Maverick\Http\Exception\HttpExceptionInterface;

class HttpExceptionHandler extends Handler
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
