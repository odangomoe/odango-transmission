<?php


namespace Odango\Transmission\Service;


use BitCommunism\Doctrine\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Odango\Transmission\Entity\Torrent;
use Transmission\Transmission;

class TransmissionService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Transmission
     */
    private $transmission;

    public function __construct(EntityManager $em, Transmission $transmission)
    {
        $this->em = $em;
        $this->transmission = $transmission;
    }

    public function updateTorrents() {
        /** @var ObjectRepository $torrentRepo */
        $torrentRepo = $this->em->getRepository(Torrent::class);

        $torrents = $torrentRepo->findBy(['transmissionId' => null]);

        /** @var Torrent $torrent */
        foreach ($torrents as $torrent) {
            try {
                $transmissionTorrent = $this->transmission->add($torrent->getTorrentPath(), false, $torrent->getDownloadPath());
                $torrent->setTransmissionId($transmissionTorrent->getId());
                $this->em->persist($torrent);
            } catch (\Exception $e) {
            }
        }

        $this->em->flush();

        $transmissionTorrents = $this->transmission->all();

        foreach ($transmissionTorrents as $transmissionTorrent) {
            $torrent = $torrentRepo->findOneBy(['infoHash' => strtolower($transmissionTorrent->getHash())]);

            if (null === $torrent) {
                continue;
            }

            var_dump($transmissionTorrent->getPercentDone());

            $torrent->setInfo($transmissionTorrent);
            $this->em->persist($torrent);
        }

        $this->em->flush();
    }
}
