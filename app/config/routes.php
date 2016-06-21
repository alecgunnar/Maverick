<?php

return [
    'index' => [
        'path' => '/hello/{name}',
        'handler' => function($req, $res) {
            $res->getBody()->write('Hello, ' . ucfirst($req->getAttribute('name')) . '!');
            return $res;
        }
    ]
];
