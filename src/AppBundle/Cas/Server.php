<?php

namespace AppBundle\Cas;

class Server implements ServerInterface
{
    private $version;
    private $host;
    private $port;
    private $context;

    public function __construct($version, $host, $port, $context)
    {
        $this->version = $version;
        $this->host    = $host;
        $this->port    = $port;
        $this->context = $context;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getContext()
    {
        return $this->context;
    }
}
