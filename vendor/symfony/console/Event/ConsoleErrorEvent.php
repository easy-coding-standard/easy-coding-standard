<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\Console\Event;

use ECSPrefix20211002\Symfony\Component\Console\Command\Command;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface;
/**
 * Allows to handle throwables thrown while running a command.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class ConsoleErrorEvent extends \ECSPrefix20211002\Symfony\Component\Console\Event\ConsoleEvent
{
    private $error;
    private $exitCode;
    public function __construct(\ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface $output, \Throwable $error, \ECSPrefix20211002\Symfony\Component\Console\Command\Command $command = null)
    {
        parent::__construct($command, $input, $output);
        $this->error = $error;
    }
    public function getError() : \Throwable
    {
        return $this->error;
    }
    /**
     * @param \Throwable $error
     */
    public function setError($error) : void
    {
        $this->error = $error;
    }
    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode) : void
    {
        $this->exitCode = $exitCode;
        $r = new \ReflectionProperty($this->error, 'code');
        $r->setAccessible(\true);
        $r->setValue($this->error, $this->exitCode);
    }
    public function getExitCode() : int
    {
        return $this->exitCode ?? (\is_int($this->error->getCode()) && 0 !== $this->error->getCode() ? $this->error->getCode() : 1);
    }
}
