<?php


namespace Odango\Transmission\Handler;

use Odango\Transmission\Service\OdangoService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;

use function GuzzleHttp\Psr7\stream_for;

class Home
{
    /**
     * @var OdangoService
     */
    private $odangoService;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * Home constructor.
     *
     * @param   OdangoService  $odangoService
     * @param   Environment    $twig
     */
    public function __construct(OdangoService $odangoService, Environment $twig)
    {
        $this->odangoService = $odangoService;
        $this->twig          = $twig;
    }

    public function home(RequestInterface $request, ResponseInterface $response)
    {
        $collections = $this->odangoService->getLatestCollections();

        return
            $response->withBody(
                stream_for(
                    $this->twig->render(
                        'home.html.twig',
                        [
                            'collections' => $collections,
                        ]
                    )
                )
            );
    }

    public function torrent(RequestInterface $request, ResponseInterface $response, $vars)
    {
        $data = json_decode(file_get_contents('https://odango.moe/api/torrent/by-anime/'.$vars['id']), true);

        $byGroup = [];

        foreach ($data['torrent-sets'] as $torrentSet) {
            $group = $torrentSet['metadata']['group'] ?? 'unknown';

            if ( ! isset($byGroup[$group])) {
                $byGroup[$group] = [];
            }

            $byGroup[$group][] = $torrentSet;
        }

        $collections      = $this->odangoService->getByAnimeId($vars['id']);
        $collectionByHash = [];

        foreach ($collections as $collection) {
            $collectionByHash[$collection->getAnimeHash()] = $collection;
        }

        return
            $response->withBody(
                stream_for(
                    $this->twig->render(
                        'torrents.html.twig',
                        [
                            'sets'        => $byGroup,
                            'anime'       => $data['anime'],
                            'collections' => $collectionByHash,
                        ]
                    )
                )
            );
    }
}