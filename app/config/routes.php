<?php

return [
    'index' => [
        'path' => '/hello/{name}',
        'handler' => function($req, $res) {
            $res->getBody()->write('Hello, ' . $reg->getAttribute('name') . '!');
            return $res;
        }
    ]
];
