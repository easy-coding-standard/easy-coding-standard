<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Console\Event;

use ECSPrefix20210507\Symfony\Component\Console\Command\Command;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
/**
 * Allows to handle throwables thrown while running a command.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class ConsoleErrorEvent extends \ECSPrefix20210507\Symfony\Component\Console\Event\ConsoleEvent
{
    private $error;
    private $exitCode;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @param \Throwable $error
     * @param \ECSPrefix20210507\Symfony\Component\Console\Command\Command $command
     */
    public function __construct($input, $output, $error, $command = null)
    {
        parent::__construct($command, $input, $output);
        $this->error = $error;
    }
    /**
     * @return \Throwable
     */
    public function getError()
    {
        return $this->error;
    }
    /**
     * @return void
     * @param \Throwable $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
    /**
     * @return void
     * @param int $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
        $r = new \ReflectionProperty($this->error, 'code');
        $r->setAccessible(\true);
        $r->setValue($this->error, $this->exitCode);
    }
    /**
     * @return int
     */
    public function getExitCode()
    {
        return null !== $this->exitCode ? $this->exitCode : (\is_int($this->error->getCode()) && 0 !== $this->error->getCode() ? $this->error->getCode() : 1);
    }
}
