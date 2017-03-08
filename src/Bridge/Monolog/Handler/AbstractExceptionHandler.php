<?php

namespace Bridge\Monolog\Handler;

use Symfony\Bridge\Monolog\Handler\SwiftMailerHandler;

abstract class AbstractExceptionHandler extends SwiftMailerHandler
{
    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        $handle = false;

        foreach ($records as $record) {
            if ($this->match($record)) {
                $handle = true;

                break;
            }
        }

        if ($handle) {
            parent::handleBatch($records);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        if ($this->match($record)) {
            return parent::handle($record);
        }

        return false;
    }

    abstract protected function match(array $record);
}
