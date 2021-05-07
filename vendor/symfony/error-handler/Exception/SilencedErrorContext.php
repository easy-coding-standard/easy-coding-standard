<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\ErrorHandler\Exception;

/**
 * Data Object that represents a Silenced Error.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SilencedErrorContext implements \JsonSerializable
{
    public $count = 1;
    private $severity;
    private $file;
    private $line;
    private $trace;
    /**
     * @param int $severity
     * @param string $file
     * @param int $line
     * @param int $count
     */
    public function __construct($severity, $file, $line, array $trace = [], $count = 1)
    {
        $this->severity = $severity;
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
        $this->count = $count;
    }
    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }
    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
    /**
     * @return mixed[]
     */
    public function getTrace()
    {
        return $this->trace;
    }
    /**
     * @return mixed[]
     */
    public function jsonSerialize()
    {
        return ['severity' => $this->severity, 'file' => $this->file, 'line' => $this->line, 'trace' => $this->trace, 'count' => $this->count];
    }
}
