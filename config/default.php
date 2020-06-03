<?php

namespace Odango\Transmission;


return [
    'debug'     => false,
    'db.config' => [
        'url' => 'sqlite:///../test.sqlite',
    ],

    'torrent.engine'        => 'transmission',
    'torrent.download-path' => '/anime/series',

    'rtorrent.xmlrpc' => 'http://localhost/RPC2',
    'rtorrent.tags'   => ['odango', 'anime'],

    'transmission.host' => 'localhost',
    'transmission.port' => 9091,
    'transmission.path' => '/transmission/rpc',

    # You can add auth info with this
    # 'transmission.auth' => ['username', 'password']
];