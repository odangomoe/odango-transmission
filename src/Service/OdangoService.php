<?php


namespace Odango\Transmission\Service;


use DI\Container;
use Doctrine\ORM\EntityManager;
use Odango\Transmission\Entity\Collection;
use Odango\Transmission\Entity\Torrent;

class OdangoService
{
    private $em;
    private $downloadPath;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->downloadPath = $container->get('torrent.download-path');
    }

    public function getLatestCollections()
    {
        $collectionName = Collection::class;

        $query = $this->em->createQuery(
            "SELECT DISTINCT collection FROM {$collectionName} collection JOIN collection.torrents torrent ORDER BY torrent.created DESC"
        );
        $query->setMaxResults(20);

        return $query->getResult();
    }

    public function subscribeToCollection($animeId, $seriesHash, $friendlyName = null)
    {
        $collection = new Collection();

        $collection->setAnimeId($animeId);
        $collection->setAnimeHash($seriesHash);
        $collection->setFriendlyName($friendlyName ?? $seriesHash);

        $this->em->persist($collection);
        $this->em->flush($collection);

        return $collection->getId();
    }

    public function unsubscribeCollection($collectionId)
    {
        $collection = $this->em->find(Collection::class, $collectionId);

        if ( ! $collection) {
            return;
        }

        $this->em->remove($collection);
        $this->em->flush($collection);
    }

    public function updateCollections()
    {
        /** @var ObjectRepository $repo */
        $repo = $this->em->getRepository(Collection::class);

        $collections = $repo->findAll();

        foreach ($collections as $collection) {
            $this->updateCollection($collection);
        }
    }

    public function updateCollection(Collection $collection)
    {
        $data = json_decode(
            file_get_contents('https://odango.moe/api/torrent/by-anime/'.$collection->getAnimeId()),
            true
        );

        foreach ($data['torrent-sets'] as $torrentSet) {
            if ($torrentSet['hash'] !== $collection->getAnimeHash()) {
                continue;
            }

            $this->updateTorrents($collection, $torrentSet['torrents']);
        }
    }

    public function updateTorrents(Collection $collection, $torrents)
    {
        $torrentEntities = $collection->getTorrents();

        $hasTorrent = [];


        /** @var Torrent $torrentEntity */
        foreach ($torrentEntities as $torrentEntity) {
            $hasTorrent[strtolower($torrentEntity->getInfoHash())] = true;
        }

        foreach ($torrents as $torrent) {
            if (isset($hasTorrent[strtolower($torrent['info-hash'])])) {
                continue;
            }

            $torrentEntity = new Torrent();
            $torrentEntity->setName($torrent['title']);
            $torrentEntity->setInfoHash(strtolower($torrent['info-hash']));
            $torrentEntity->setTorrentPath($torrent['nyaa']['torrent']);

            $downloadPath = $this->downloadPath;
            $torrentEntity->setDownloadName(
                $torrent['metadata']['name'].' - '.($torrent['metadata']['group'] ?? 'unknown')
            );

            if ($torrent['metadata']['type'] === 'ep' || count($torrents) > 1) {
                $downloadPath .= '/'.$torrentEntity->getDownloadName();
            }

            $torrentEntity->setDownloadPath($downloadPath);
            $torrentEntity->setCollection($collection);

            $this->em->persist($torrentEntity);
        }

        $this->em->persist($collection);
        $this->em->flush($collection);
    }

    /**
     * @param $animeId
     *
     * @return Collection[]
     */
    public function getByAnimeId($animeId)
    {
        return $this->em->getRepository(Collection::class)->findBy(['animeId' => $animeId]);
    }
}