<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210516\Symfony\Component\Console\Event;

use ECSPrefix20210516\Symfony\Component\Console\Command\Command;
use ECSPrefix20210516\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210516\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author marie <marie@users.noreply.github.com>
 */
final class ConsoleSignalEvent extends \ECSPrefix20210516\Symfony\Component\Console\Event\ConsoleEvent
{
    private $handlingSignal;
    /**
     * @param int $handlingSignal
     */
    public function __construct(\ECSPrefix20210516\Symfony\Component\Console\Command\Command $command, \ECSPrefix20210516\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210516\Symfony\Component\Console\Output\OutputInterface $output, $handlingSignal)
    {
        $handlingSignal = (int) $handlingSignal;
        parent::__construct($command, $input, $output);
        $this->handlingSignal = $handlingSignal;
    }
    /**
     * @return int
     */
    public function getHandlingSignal()
    {
        return $this->handlingSignal;
    }
}
