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

/**
 * FormatterAwareInterface
 *
 * @package Solital\Core\Logger
 */
interface FormatterAwareInterface
{
    /**
     * @param  FormatterInterface
     * @return self
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface;
}
