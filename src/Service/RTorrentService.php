<?php


namespace Odango\Transmission\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use Odango\Transmission\Entity\Torrent;
use Odango\Transmission\Helper\RTorrent;

class RTorrentService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RTorrent
     */
    private $rtorrent;

    public function __construct(EntityManager $em, RTorrent $rtorrent)
    {
        $this->em       = $em;
        $this->rtorrent = $rtorrent;
    }

    public function updateTorrents()
    {
        /** @var ObjectRepository $torrentRepo */
        $torrentRepo = $this->em->getRepository(Torrent::class);

        $torrents = $torrentRepo->findBy(['transmissionId' => null]);

        /** @var Torrent $torrent */
        foreach ($torrents as $torrent) {
            try {
                $this->rtorrent->addTorrent(
                    $torrent->getTorrentPath(),
                    $torrent->getDownloadPath(),
                    $torrent->getDownloadName()
                );
                // rTorrent doesn't use any magic id's
                $torrent->setTransmissionId(1);
                $this->em->persist($torrent);
            } catch (\Exception $e) {
                var_dump($e);
            }
        }

        $this->em->flush();

        $rtorrentTorrents = $this->rtorrent->getTorrents();

        foreach ($rtorrentTorrents as $rtorrentTorrent) {
            $torrent = $torrentRepo->findOneBy(['infoHash' => strtolower($rtorrentTorrent->getHash())]);

            if (null === $torrent) {
                continue;
            }

            $torrent->setInfo($rtorrentTorrent);
            $this->em->persist($torrent);
        }

        $this->em->flush();
    }
}