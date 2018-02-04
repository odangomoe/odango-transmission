<?php


namespace Odango\Transmission\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Collection
 * @package Odango\Transmission\Entity
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Collection
{
    public function __construct()
    {
        $this->torrents = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $animeId;

    /**
     * @ORM\Column(type="string")
     */
    private $animeHash;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $friendlyName;

    /**
     * @ORM\OneToMany(targetEntity="Torrent", mappedBy="collection", cascade={"remove", "persist", "refresh"})
     */
    private $torrents;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAnimeId()
    {
        return $this->animeId;
    }

    /**
     * @param mixed $animeId
     */
    public function setAnimeId($animeId): void
    {
        $this->animeId = $animeId;
    }

    /**
     * @return mixed
     */
    public function getAnimeHash()
    {
        return $this->animeHash;
    }

    /**
     * @param mixed $animeHash
     */
    public function setAnimeHash($animeHash): void
    {
        $this->animeHash = $animeHash;
    }

    /**
     * @return mixed
     */
    public function getTorrents()
    {
        return $this->torrents;
    }

    /**
     * @param mixed $torrents
     */
    public function setTorrents($torrents): void
    {
        $this->torrents = $torrents;
    }

    public function addTorrent(Torrent $torrent) {
        $this->torrents->add($torrent);
    }

    /**
     * @param string $friendlyName
     */
    public function setFriendlyName(string $friendlyName): void
    {
        $this->friendlyName = $friendlyName;
    }

    /**
     * @return string
     */
    public function getFriendlyName(): string
    {
        return $this->friendlyName;
    }
}