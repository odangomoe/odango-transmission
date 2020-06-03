<?php

use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use fXmlRpc\Transport\HttpAdapterTransport;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Odango\Transmission\Helper\RTorrent;
use Transmission\Client;
use Transmission\Transmission;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use Twig\Loader\LoaderInterface;

use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'rtorrent.xmlrpc-client' => factory(
        function (Container $c) {
            return new \fXmlRpc\Client(
                $c->get('rtorrent.xmlrpc'),
                new HttpAdapterTransport(
                    new GuzzleMessageFactory(),
                    new \Http\Adapter\Guzzle6\Client(new \GuzzleHttp\Client())
                )
            );
        }
    ),

    RTorrent::class => create(RTorrent::class)
        ->constructor(get('rtorrent.xmlrpc-client')),
    Client::class   => function (Container $c) {
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

    'doctrine.config' => function (Container $c) {
        return Setup::createAnnotationMetadataConfiguration([__DIR__.'/../src/Entity'], $c->get('debug'), null, null, false);
    },

    EntityManager::class => function (Container $c) {
        return EntityManager::create($c->get('db.config'), $c->get('doctrine.config'));
    },

    FilesystemLoader::class => create()->constructor([__DIR__ . "/../templates"]),
    LoaderInterface::class => get(FilesystemLoader::class),
    Environment::class => function(Container $c) {
        return new Environment($c->get(LoaderInterface::class));
    }
];
