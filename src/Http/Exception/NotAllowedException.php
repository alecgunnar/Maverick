<?php

namespace Maverick\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;

class NotAllowedException extends HttpException
{
    /**
     * @var string
     */
    const NOT_ALLOWED_FORMAT = 'You are not allowed to make this request via %s.';

    public function __construct(ServerRequestInterface $request)
    {
        $msg = sprintf(self::NOT_ALLOWED_FORMAT, strtoupper($request->getMethod()));
        parent::__construct($msg, $request);
    }

    public function getStatusCode(): int
    {
        return 405;
    }
}
