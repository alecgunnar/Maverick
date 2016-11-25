<?php

namespace Maverick\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;

class NotAllowedException extends HttpException
{
    public function __construct(ServerRequestInterface $request)
    {
        $msg = sprintf(
            'You are not allowed to make this request via %s.',
            strtoupper($request->getMethod())
        );

        parent::__construct($msg, $request);
    }

    public function getStatusCode(): int
    {
        return 405;
    }
}
