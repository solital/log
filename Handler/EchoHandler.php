<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Solital\Core\Logger
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types=1);

namespace Solital\Core\Logger\Handler;

use Solital\Core\Logger\Entry\LogEntryInterface;

/**
 * echo log message to STDOUT
 *
 * @package Solital\Core\Logger
 */
class EchoHandler extends HandlerAbstract
{
    /**
     * @param LogEntryInterface $entry
     * 
     * @return void
     */
    protected function write(LogEntryInterface $entry): void
    {
        echo $this->getFormatter()->format($entry);
    }
}