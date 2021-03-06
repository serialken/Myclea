<?php

namespace Bridge\Monolog\Handler;

use Doctrine\DBAL\DBALException;

class DatabaseExceptionHandler extends AbstractExceptionHandler
{
    protected function match(array $record)
    {
        return isset($record['context']['exception']) && $record['context']['exception'] instanceof DBALException;
    }
}
