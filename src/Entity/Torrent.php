<?php


namespace Odango\Transmission\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Torrent
 * @package Odango\Transmission\Entity
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Torrent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $infoHash;

    /**
     * @ORM\Column(type="string")
     */
    private $downloadPath;

    /**
     * @ORM\Column(type="string")
     */
    private $torrentPath;

    /**
     * @ORM\ManyToOne(targetEntity="Collection")
     */
    private $collection;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $transmissionId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetimetz")
     */
    private $created;

    public function __construct()
    {
        $this->created = new \DateTime();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getInfoHash()
    {
        return $this->infoHash;
    }

    /**
     * @return mixed
     */
    public function getDownloadPath()
    {
        return $this->downloadPath;
    }

    /**
     * @return mixed
     */
    public function getTorrentPath()
    {
        return $this->torrentPath;
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return mixed
     */
    public function getTransmissionId()
    {
        return $this->transmissionId;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return unserialize($this->info);
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $infoHash
     */
    public function setInfoHash($infoHash): void
    {
        $this->infoHash = $infoHash;
    }

    /**
     * @param mixed $downloadPath
     */
    public function setDownloadPath($downloadPath): void
    {
        $this->downloadPath = $downloadPath;
    }

    /**
     * @param mixed $torrentPath
     */
    public function setTorrentPath($torrentPath): void
    {
        $this->torrentPath = $torrentPath;
    }

    /**
     * @param mixed $collection
     */
    public function setCollection($collection): void
    {
        $this->collection = $collection;
    }

    /**
     * @param mixed $transmissionId
     */
    public function setTransmissionId($transmissionId): void
    {
        $this->transmissionId = $transmissionId;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = serialize($info);
    }
}