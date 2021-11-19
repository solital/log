<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Solital\Core\Logger
 * @copyright Copyright (c) 2019 Hong Zhang
 */

declare(strict_types=1);

namespace Solital\Core\Logger\Formatter;

use Solital\Core\Logger\Entry\LogEntryInterface;

/**
 * FormatterInterface
 *
 * @package Solital\Core\Logger
 */
interface FormatterInterface
{
    /**
     * convert to formatted message
     *
     * @param  LogEntryInterface $entry
     * 
     * @return string
     */
    public function format(LogEntryInterface $entry): string;
}
