<?php

namespace Odango\Transmission;

use function BitCommunism\Doctrine\connections;
use function DI\add;
use DI\Container;
use function DI\object;
use Odango\Transmission\Service\OdangoService;
use Odango\Transmission\Service\TransmissionService;
use Transmission\Client;
use Transmission\Transmission;


return array_merge([
    'marx.calls' => add([
        'odango' => function ($arguments, OdangoService $odangoService, TransmissionService $transmissionService) {
            $first = $arguments[1] ?? 'none';
            echo "Executing: {$first}\n";

            if ($first === 'update') {
                $odangoService->updateCollections();
                return;
            }

            if ($first === 'update-transmission') {
                $transmissionService->updateTorrents();
                return;
            }
        }
    ]),

    'modules' => [
        'http',
        'twig',
        'doctrine',
    ],

    'transmission.host' => 'localhost',
    'transmission.port' => 9091,
    'transmission.path' => '/transmission/rpc',
    'transmission.download-path' => '/storage',

    # You can add auth info with this
    # 'transmission.auth' => ['username', 'password']


    Client::class => function(Container $c) {
        $client = new Client(
            $c->get('transmission.host'),
            $c->get('transmission.port'),
            $c->get('transmission.path')
        );

        if ($c->has('transmission.auth')) {
            [$username, $password] = $c->get('transmission.auth');
            $client->authenticate($username, $password);
        }

        return $client;
    },

    Transmission::class => function (Client $client) {
        $transmission = new Transmission();
        $transmission->setClient($client);

        return $transmission;
    },
], connections([
    'default' => [
        'entity-paths' => add([]),
        'connection' => [
            'user' => 'root',
            'driver' => 'pdo_mysql',
            'dbname' => 'odango-transmission',
        ],
    ],
]));