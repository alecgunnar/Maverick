<?php

namespace Maverick\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends HttpException
{
    public function __construct(ServerRequestInterface $request)
    {
        $msg = sprintf(
            'A route does not exist for "%s %s".',
            strtoupper($request->getMethod()),
            $request->getUri()->getPath()
        );
        
        parent::__construct($msg, $request);
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
