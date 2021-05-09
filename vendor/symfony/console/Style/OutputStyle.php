<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Component\Console\Style;

use ECSPrefix20210509\Symfony\Component\Console\Formatter\OutputFormatterInterface;
use ECSPrefix20210509\Symfony\Component\Console\Helper\ProgressBar;
use ECSPrefix20210509\Symfony\Component\Console\Output\ConsoleOutputInterface;
use ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface;
/**
 * Decorates output to add console style guide helpers.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class OutputStyle implements \ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface, \ECSPrefix20210509\Symfony\Component\Console\Style\StyleInterface
{
    private $output;
    public function __construct(\ECSPrefix20210509\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->output = $output;
    }
    /**
     * {@inheritdoc}
     * @param int $count
     */
    public function newLine($count = 1)
    {
        $count = (int) $count;
        $this->output->write(\str_repeat(\PHP_EOL, $count));
    }
    /**
     * @return ProgressBar
     * @param int $max
     */
    public function createProgressBar($max = 0)
    {
        $max = (int) $max;
        return new \ECSPrefix20210509\Symfony\Component\Console\Helper\ProgressBar($this->output, $max);
    }
    /**
     * {@inheritdoc}
     * @param bool $newline
     * @param int $type
     */
    public function write($messages, $newline = \false, $type = self::OUTPUT_NORMAL)
    {
        $newline = (bool) $newline;
        $type = (int) $type;
        $this->output->write($messages, $newline, $type);
    }
    /**
     * {@inheritdoc}
     * @param int $type
     */
    public function writeln($messages, $type = self::OUTPUT_NORMAL)
    {
        $type = (int) $type;
        $this->output->writeln($messages, $type);
    }
    /**
     * {@inheritdoc}
     * @param int $level
     */
    public function setVerbosity($level)
    {
        $level = (int) $level;
        $this->output->setVerbosity($level);
    }
    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    {
        return $this->output->getVerbosity();
    }
    /**
     * {@inheritdoc}
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        $decorated = (bool) $decorated;
        $this->output->setDecorated($decorated);
    }
    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }
    /**
     * {@inheritdoc}
     */
    public function setFormatter(\ECSPrefix20210509\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
    {
        $this->output->setFormatter($formatter);
    }
    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        return $this->output->getFormatter();
    }
    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return $this->output->isQuiet();
    }
    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return $this->output->isVerbose();
    }
    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return $this->output->isVeryVerbose();
    }
    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->output->isDebug();
    }
    protected function getErrorOutput()
    {
        if (!$this->output instanceof \ECSPrefix20210509\Symfony\Component\Console\Output\ConsoleOutputInterface) {
            return $this->output;
        }
        return $this->output->getErrorOutput();
    }
}
