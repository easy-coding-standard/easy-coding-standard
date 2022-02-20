<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220220\Symfony\Component\Console\Event;

use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220220\Symfony\Contracts\EventDispatcher\Event;
/**
 * Allows to inspect input and output of a command.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
class ConsoleEvent extends \ECSPrefix20220220\Symfony\Contracts\EventDispatcher\Event
{
    protected $command;
    private $input;
    private $output;
    public function __construct(?\ECSPrefix20220220\Symfony\Component\Console\Command\Command $command, \ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }
    /**
     * Gets the command that is executed.
     */
    public function getCommand() : ?\ECSPrefix20220220\Symfony\Component\Console\Command\Command
    {
        return $this->command;
    }
    /**
     * Gets the input instance.
     */
    public function getInput() : \ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface
    {
        return $this->input;
    }
    /**
     * Gets the output instance.
     */
    public function getOutput() : \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface
    {
        return $this->output;
    }
}
