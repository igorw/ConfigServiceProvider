<?php

return [
    'db.options' => [
        'host' => '127.0.0.1',
        'dbname' => 'mydatabase',
        'user' => 'root',
        'password' => NULL,
    ],
    'myproject.test' => [
        'param2' => '456',
        'param3' => [
            'param2B' => '456',
            'param2C' => '456',
        ],
        'param4' => [4, 5, 6],
        'param5' => '456',
    ],
    'test.noparent.key' => [
        'test' => [1, 2, 3, 4],
    ],
];
