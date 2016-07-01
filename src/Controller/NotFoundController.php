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

class NotFoundController extends AbstractController
{
    /**
     * @inheritDoc
     */
    protected function beforeNext()
    {
        $this->response = $this->response->withStatus(404);

        $body = <<<HERE
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Not Found &mdash; 404</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <h1>Not Found &mdash; 404</h1>
            <p>The page you are looking for was not found.</p>
        </div>
    </body>
</html>
HERE;

        $this->print($body);
    }
}
