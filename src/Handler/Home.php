<?php


namespace Odango\Transmission\Handler;


use BitCommunism\Twig\Handler\Twig;
use Odango\Transmission\Service\OdangoService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Home extends Twig
{
    public function home(RequestInterface $request, ResponseInterface $response, OdangoService $odangoService) {
        $collections = $odangoService->getLatestCollections();

        return $this->template('home.html.twig', [
            'collections' => $collections,
        ]);
    }

    public function torrent($vars, RequestInterface $request, ResponseInterface $response, OdangoService $odangoService) {
        $data = json_decode(file_get_contents('https://odango.moe/api/torrent/by-anime/' . $vars['id']), true);

        $byGroup = [];

        foreach ($data['torrent-sets'] as $torrentSet) {
            $group = $torrentSet['metadata']['group'] ?? 'unkown';

            if (!isset($byGroup[$group])) {
                $byGroup[$group] = [];
            }

            $byGroup[$group][] = $torrentSet;
        }

        $collections = $odangoService->getByAnimeId($vars['id']);
        $collectionByHash = [];

        foreach ($collections as $collection) {
            $collectionByHash[$collection->getAnimeHash()] = $collection;
        }

        return $this->template('torrents.html.twig', [
            'sets' => $byGroup,
            'anime' => $data['anime'],
            'collections' =>  $collectionByHash,
        ]);
    }
}