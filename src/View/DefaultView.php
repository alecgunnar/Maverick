<?php

namespace Maverick\View;

abstract class DefaultView implements ViewInterface
{
    protected $content = <<<BODY
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>%s</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <style>
        body {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
        }
        
        .container {
            max-width: 600px;
        }
        </style>
    </head>
    <body>
        <div class="container">
            <header class="page-header"><h1>%s</h1></header>
            <p class="lead">%s</p>
        </div>
    </body>
</html>
BODY;

    final public function render(array $params = []): string
    {
        $title = $this->getTitle();
        $content = $this->getContent();

        return sprintf($this->content, $title, $title, $content);
    }

    abstract protected function getTitle(): string;

    abstract protected function getContent(): string;
}
