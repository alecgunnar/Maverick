<?php
/**
 * Maverick Framework
 *
 * @author Alec Carpenter <alecgunnar@gmail.com>
 */
declare(strict_types=1);

namespace Maverick\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Maverick\Router\AbstractRouter;
use Maverick\Middleware\RouterMiddleware;

class NotAllowedController extends AbstractController
{
    /**
     * @inheritDoc
     */
    protected function beforeNext()
    {
        // This needs to respond with the allowed methods too...
        $this->response = $this->response->withStatus(405)
            ->withHeader('Allow', implode(
                ',',
                $this->request->getAttribute(RouterMiddleware::ALLOWED_METHODS, [])
            ));

        $body = <<<HERE
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Method Not Allowed &mdash; 405</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <h1>Method Not Allowed &mdash; 405</h1>
            <p>You may not access this page this way.</p>
        </div>
    </body>
</html>
HERE;

        $this->print($body);
    }
}
