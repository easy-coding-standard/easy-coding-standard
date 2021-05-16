<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210516\Symfony\Component\Console;

use ECSPrefix20210516\Symfony\Component\Console\Command\Command;
use ECSPrefix20210516\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210516\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class SingleCommandApplication extends \ECSPrefix20210516\Symfony\Component\Console\Command\Command
{
    private $version = 'UNKNOWN';
    private $autoExit = \true;
    private $running = \false;
    /**
     * @return $this
     * @param string $version
     */
    public function setVersion($version)
    {
        $version = (string) $version;
        $this->version = $version;
        return $this;
    }
    /**
     * @final
     * @return $this
     * @param bool $autoExit
     */
    public function setAutoExit($autoExit)
    {
        $autoExit = (bool) $autoExit;
        $this->autoExit = $autoExit;
        return $this;
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface|null $input
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @return int
     */
    public function run($input = null, $output = null)
    {
        if ($this->running) {
            return parent::run($input, $output);
        }
        // We use the command name as the application name
        $application = new \ECSPrefix20210516\Symfony\Component\Console\Application($this->getName() ?: 'UNKNOWN', $this->version);
        $application->setAutoExit($this->autoExit);
        // Fix the usage of the command displayed with "--help"
        $this->setName($_SERVER['argv'][0]);
        $application->add($this);
        $application->setDefaultCommand($this->getName(), \true);
        $this->running = \true;
        try {
            $ret = $application->run($input, $output);
        } finally {
            $this->running = \false;
        }
        return isset($ret) ? $ret : 1;
    }
}
