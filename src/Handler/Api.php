<?php


namespace Odango\Transmission\Handler;


use BitCommunism\Doctrine\EntityManager;
use Odango\Transmission\Entity\Collection;
use Odango\Transmission\Service\OdangoService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\stream_for;

class Api
{
    /**
     * @var OdangoService
     */
    private $odangoService;

    public function __construct(OdangoService $odangoService)
    {
        $this->odangoService = $odangoService;
    }

    public function subscribe(RequestInterface $request, ResponseInterface $response, EntityManager $em)
    {
        $body = json_decode($request->getBody(), true);

        if (!isset($body['anime-id']) || !isset($body['hash'])) {
            return $response->withStatus(400);
        }

        $id = $this->odangoService->subscribeToCollection($body['anime-id'], $body['hash'], $body['name'] ?? $body['hash']);

        $this->odangoService->updateCollection($em->find(Collection::class, $id));

        return $this->json($response, [
            'id' => $id,
        ]);
    }

    public function unsubscribe(RequestInterface $request, ResponseInterface $response)
    {
        $body = json_decode($request->getBody(), true);

        if (!isset($body['id'])) {
            return $response->withStatus(400);
        }

        $this->odangoService->unsubscribeCollection($body['id']);
    }

    public function json(ResponseInterface $response, $data)
    {
        return $response->withBody(
            stream_for(json_encode($data))
        )
            ->withHeader('Content-Type', 'application/json');
    }
}