<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\Console\Output;

use ECSPrefix20210510\Symfony\Component\Console\Exception\InvalidArgumentException;
use ECSPrefix20210510\Symfony\Component\Console\Formatter\OutputFormatterInterface;
/**
 * A BufferedOutput that keeps only the last N chars.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class TrimmedBufferOutput extends \ECSPrefix20210510\Symfony\Component\Console\Output\Output
{
    private $maxLength;
    private $buffer = '';
    /**
     * @param int|null $verbosity
     * @param int $maxLength
     * @param bool $decorated
     */
    public function __construct($maxLength, $verbosity = self::VERBOSITY_NORMAL, $decorated = \false, \ECSPrefix20210510\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter = null)
    {
        $maxLength = (int) $maxLength;
        $decorated = (bool) $decorated;
        if ($maxLength <= 0) {
            throw new \ECSPrefix20210510\Symfony\Component\Console\Exception\InvalidArgumentException(\sprintf('"%s()" expects a strictly positive maxLength. Got %d.', __METHOD__, $maxLength));
        }
        parent::__construct($verbosity, $decorated, $formatter);
        $this->maxLength = $maxLength;
    }
    /**
     * Empties buffer and returns its content.
     *
     * @return string
     */
    public function fetch()
    {
        $content = $this->buffer;
        $this->buffer = '';
        return $content;
    }
    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer .= $message;
        if ($newline) {
            $this->buffer .= \PHP_EOL;
        }
        $this->buffer = \substr($this->buffer, 0 - $this->maxLength);
    }
}
