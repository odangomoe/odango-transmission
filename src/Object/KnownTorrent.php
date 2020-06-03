<?php


namespace Odango\Transmission\Object;


class KnownTorrent
{
    private $name;
    private $hash;
    private $ratio;
    private $downloadRate;
    private $uploadRate;
    private $size;
    private $bytesDone;

    /**
     * KnownTorrent constructor.
     *
     * @param $name
     * @param $hash
     * @param $ratio
     * @param $downloadRate
     * @param $uploadRate
     * @param $size
     * @param $bytesDone
     */
    public function __construct($name, $hash, $ratio, $downloadRate, $uploadRate, $size, $bytesDone)
    {
        $this->name         = $name;
        $this->hash         = $hash;
        $this->ratio        = $ratio;
        $this->downloadRate = $downloadRate;
        $this->uploadRate   = $uploadRate;
        $this->size         = $size;
        $this->bytesDone    = $bytesDone;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
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
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @return mixed
     */
    public function getDownloadRate()
    {
        return $this->downloadRate;
    }

    /**
     * @return mixed
     */
    public function getUploadRate()
    {
        return $this->uploadRate;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getBytesDone()
    {
        return $this->bytesDone;
    }

    public function getPercentDone()
    {
        return round(100 * ($this->bytesDone / $this->size), 2);
    }
}