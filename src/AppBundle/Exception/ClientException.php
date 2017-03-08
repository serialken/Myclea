<?php

namespace AppBundle\Exception;

class ClientException extends \RuntimeException
{
    public static function create(array $error)
    {
        $instance = new static();

        $instance->message = $error['message'];
        $instance->code    = $error['type'];
        $instance->file    = isset($error['fileName']) ? $error['fileName'] : 'Unknown';
        $instance->line    = isset($error['lineNumber']) ? $error['lineNumber'] : 'Unknown';

        return $instance;
    }
}
