<?php

namespace AppBundle\Profile\Exception;

class ProfileNumberNotFound extends \RuntimeException
{
    private $displays;

    public function __construct($message = '', $code = 0, \Exception $previous = null, array $displays = [])
    {
        parent::__construct($message, $code, $previous);

        $this->displays = $displays;
    }
}
