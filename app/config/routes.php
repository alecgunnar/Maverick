<?php

return [
    'index' => [
        'path' => '/hello/{name}',
        'handler' => function($req, $res) {
            $res->getBody()->write('Hello, ' . $req->getAttribute('name') . '!');
            return $res;
        }
    ]
];
