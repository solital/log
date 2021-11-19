<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Solital\Core\Logger
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types=1);

namespace Solital\Core\Logger\Processor;

use Solital\Core\Logger\Entry\LogEntryInterface;

/**
 * ProcessorAbstract
 *
 * @package Solital\Core\Logger
 */
abstract class ProcessorAbstract
{
    /**
     * make it an invokable
     *
     * @param LogEntryInterface $entry
     */
    public function __invoke(LogEntryInterface $entry)
    {
        $context = $entry->getContext();
        $entry->setContext($this->updateContext($context));
    }

    /**
     * update info in the $context
     *
     * ```php
     * protected function updateContext(array $context): array
     * {
     *     $context['bingo'] = 'wow';
     *     return $context;
     * }
     * ```
     *
     * @param array $context
     * 
     * @return array
     */
    abstract protected function updateContext(array $context): array;
}