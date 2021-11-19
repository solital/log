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
 * FormatterAwareTrait
 *
 * @package   Solital\Core\Logger
 * @interface FormatterAwareInterface
 */
trait FormatterAwareTrait
{
    /**
     * @var null|FormatterInterface
     */
    protected ?FormatterInterface $formatter = null;

    /**
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * @param FormatterInterface $formatter
     * 
     * @return self
     */
    public function setFormatter(FormatterInterface $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }
}
