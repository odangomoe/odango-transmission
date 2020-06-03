<?php

namespace Odango\Transmission;

use Odango\Transmission\Handler\Api;
use Odango\Transmission\Handler\Home;

return [
    '/torrents/{id}/{selected-name}' => [Home::class, 'torrent'],
    '/api/subscribe' => [
        'POST' => [Api::class, 'subscribe'],
    ],
    '/api/unsubscribe' => [
        'POST' => [Api::class, 'unsubscribe']
    ]
];
