<?php

return [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ],
    'myproject.test' => [
        'param1' => '123',
        'param2' => '123',
        'param3' => [
            'param2A' => '123',
            'param2B' => '123',
        ],
        'param4' => [1, 2, 3],
     ],
     'test.noparent.key' => [],
];
