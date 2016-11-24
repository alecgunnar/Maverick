<?php

namespace Maverick\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;

class NotFoundException extends HttpException
{
    /**
     * @var string
     */
    const NOT_FOUND_FORMAT = 'A route does not exist for "%s %s".';

    public function __construct(ServerRequestInterface $request)
    {
        $msg = sprintf(self::NOT_FOUND_FORMAT, strtoupper($request->getMethod()), $request->getUri()->getPath());
        parent::__construct($msg, $request);
    }

    public function getStatusCode(): int
    {
        return 404;
    }
}
