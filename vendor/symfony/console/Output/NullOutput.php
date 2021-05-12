<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210512\Symfony\Component\Console\Output;

use ECSPrefix20210512\Symfony\Component\Console\Formatter\NullOutputFormatter;
use ECSPrefix20210512\Symfony\Component\Console\Formatter\OutputFormatterInterface;
/**
 * NullOutput suppresses all output.
 *
 *     $output = new NullOutput();
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class NullOutput implements \ECSPrefix20210512\Symfony\Component\Console\Output\OutputInterface
{
    private $formatter;
    /**
     * {@inheritdoc}
     */
    public function setFormatter(\ECSPrefix20210512\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
    {
        // do nothing
    }
    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if ($this->formatter) {
            return $this->formatter;
        }
        // to comply with the interface we must return a OutputFormatterInterface
        return $this->formatter = new \ECSPrefix20210512\Symfony\Component\Console\Formatter\NullOutputFormatter();
    }
    /**
     * {@inheritdoc}
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        $decorated = (bool) $decorated;
        // do nothing
    }
    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param int $level
     */
    public function setVerbosity($level)
    {
        $level = (int) $level;
        // do nothing
    }
    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    {
        return self::VERBOSITY_QUIET;
    }
    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     * @param int $options
     */
    public function writeln($messages, $options = self::OUTPUT_NORMAL)
    {
        $options = (int) $options;
        // do nothing
    }
    /**
     * {@inheritdoc}
     * @param bool $newline
     * @param int $options
     */
    public function write($messages, $newline = \false, $options = self::OUTPUT_NORMAL)
    {
        $newline = (bool) $newline;
        $options = (int) $options;
        // do nothing
    }
}
