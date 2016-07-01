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

class WelcomeController extends AbstractController
{
    /**
     * @inheritDoc
     */
    protected function beforeNext()
    {
        $handlerLocation = __FILE__;

        $body = <<<HERE
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Welcome to Maverick</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <style>
        .navbar,
        .jumbotron {
            margin-bottom: 0;
        }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-static-top">
            <div class="container">
                <div class="navbar-header">
                    <div class="navbar-brand">
                        Maverick
                    </div>
                </div>
                <p class="navbar-text">PHP Framework</p>
            </div>
        </nav>
        <div class="jumbotron">
            <div class="container">
                <h1>Welcome to Maverick</h1>
                <p>You have successfully gotten Maverick running.</p>
            </div>
        </div>
        <div class="container">
            <h2>Ready to start developing?</h2>
            <p>To begin, start by editing this handler. You can find this handler in the following file: <code style="word-break: break-all;">$handlerLocation</code>. From there, you may begin changing the look and feel of this page.</p>
        </div>
    </body>
</html>
HERE;

        $this->print($body);
    }
}
