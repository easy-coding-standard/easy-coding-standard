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
 * Allows to manipulate the exit code of a command after its execution.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
final class ConsoleTerminateEvent extends \ECSPrefix20211002\Symfony\Component\Console\Event\ConsoleEvent
{
    private $exitCode;
    public function __construct(\ECSPrefix20211002\Symfony\Component\Console\Command\Command $command, \ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20211002\Symfony\Component\Console\Output\OutputInterface $output, int $exitCode)
    {
        parent::__construct($command, $input, $output);
        $this->setExitCode($exitCode);
    }
    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode) : void
    {
        $this->exitCode = $exitCode;
    }
    public function getExitCode() : int
    {
        return $this->exitCode;
    }
}
