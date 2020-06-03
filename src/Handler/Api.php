<?php


namespace Odango\Transmission\Handler;


use Doctrine\ORM\EntityManager;
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

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(OdangoService $odangoService, EntityManager $entityManager)
    {
        $this->odangoService = $odangoService;
        $this->entityManager = $entityManager;
    }

    public function subscribe(RequestInterface $request, ResponseInterface $response)
    {
        $body = json_decode($request->getBody(), true);

        if ( ! isset($body['anime-id']) || ! isset($body['hash'])) {
            return $response->withStatus(400);
        }

        $id = $this->odangoService->subscribeToCollection(
            $body['anime-id'],
            $body['hash'],
            $body['name'] ?? $body['hash']
        );

        /** @var Collection $collection */
        $collection = $this->entityManager->find(Collection::class, $id);
        $this->odangoService->updateCollection($collection);

        return $this->json(
            $response,
            [
                'id' => $id,
            ]
        );
    }

    public function unsubscribe(RequestInterface $request, ResponseInterface $response)
    {
        $body = json_decode($request->getBody(), true);

        if ( ! isset($body['id'])) {
            return $response->withStatus(400);
        }

        $this->odangoService->unsubscribeCollection($body['id']);
        return $this->json($response, []);
    }

    public function json(ResponseInterface $response, $data)
    {
        return $response
            ->withBody(
                stream_for(json_encode($data))
            )
            ->withHeader('Content-Type', 'application/json');
    }
}