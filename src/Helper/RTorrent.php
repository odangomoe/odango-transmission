<?php


namespace Odango\Transmission\Helper;


use fXmlRpc\Client;
use fXmlRpc\Proxy;
use fXmlRpc\Value\Base64;
use Odango\Transmission\Object\KnownTorrent;

class RTorrent
{
    private $client;
    private $proxy;

    public function __construct(Client $xmlrpc)
    {
        $this->client = $xmlrpc;
        $this->proxy  = new Proxy($this->client);
    }

    public function addTorrent($torrentPath, $downloadDir, $downloadName, $try = 3)
    {
        $params = [
            "d.custom.set=odango,yes",
            "d.custom.set=addtime," . time(),
            "d.custom1.set=\"odango,anime\"",
        ];
        if (substr($downloadDir, -strlen($downloadName)) !== $downloadName) {
            // yay, conform multi file torrents to structure
            $params[] = "d.directory_base.set=\"".$downloadDir."/".$downloadName."\"";
        } else {
            $params[] = "d.directory.set=\"$downloadDir\"";
        }

        $torrent = file_get_contents($torrentPath);

        if (!$torrent) {
            throw new \RuntimeException("Failed to read Torrent: $torrentPath");
        }

        $this->proxy->load->raw_start_verbose("", Base64::serialize($torrent), ...$params);
    }

    public function getTorrents()
    {
        $torrents = $this->proxy->d->multicall->filtered(
            "",
            "",
            "d.custom=odango",
            "d.name=",
            "d.hash=",
            "d.ratio=",
            "d.down.rate=",
            "d.up.rate=",
            "d.size_bytes=",
            "d.bytes_done=",
            "d.custom=addtime",
        );
        $known    = [];
        foreach ($torrents as $torrent) {
            $known[] = new KnownTorrent(
                $torrent[0], $torrent[1], $torrent[2], $torrent[3], $torrent[4], $torrent[5], $torrent[6]
            );
        }

        return $known;
    }
}