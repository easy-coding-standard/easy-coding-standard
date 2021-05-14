<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210514\Symfony\Component\Process\Exception;

use ECSPrefix20210514\Symfony\Component\Process\Process;
/**
 * Exception that is thrown when a process has been signaled.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class ProcessSignaledException extends \ECSPrefix20210514\Symfony\Component\Process\Exception\RuntimeException
{
    private $process;
    public function __construct(\ECSPrefix20210514\Symfony\Component\Process\Process $process)
    {
        $this->process = $process;
        parent::__construct(\sprintf('The process has been signaled with signal "%s".', $process->getTermSignal()));
    }
    /**
     * @return \Symfony\Component\Process\Process
     */
    public function getProcess()
    {
        return $this->process;
    }
    /**
     * @return int
     */
    public function getSignal()
    {
        return $this->getProcess()->getTermSignal();
    }
}
