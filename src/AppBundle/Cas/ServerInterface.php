<?php

namespace AppBundle\Cas;

interface ServerInterface
{
    /**
     * @return string
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getHost();

    /**
     * @return int
     */
    public function getPort();

    /**
     * @return string
     */
    public function getContext();
}
