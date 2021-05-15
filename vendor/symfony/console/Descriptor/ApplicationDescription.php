<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210515\Symfony\Component\Console\Descriptor;

use ECSPrefix20210515\Symfony\Component\Console\Application;
use ECSPrefix20210515\Symfony\Component\Console\Command\Command;
use ECSPrefix20210515\Symfony\Component\Console\Exception\CommandNotFoundException;
/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class ApplicationDescription
{
    const GLOBAL_NAMESPACE = '_global';
    private $application;
    private $namespace;
    private $showHidden;
    /**
     * @var array
     */
    private $namespaces;
    /**
     * @var Command[]
     */
    private $commands;
    /**
     * @var Command[]
     */
    private $aliases;
    /**
     * @param string $namespace
     * @param bool $showHidden
     */
    public function __construct(\ECSPrefix20210515\Symfony\Component\Console\Application $application, $namespace = null, $showHidden = \false)
    {
        $showHidden = (bool) $showHidden;
        $this->application = $application;
        $this->namespace = $namespace;
        $this->showHidden = $showHidden;
    }
    /**
     * @return mixed[]
     */
    public function getNamespaces()
    {
        if (null === $this->namespaces) {
            $this->inspectApplication();
        }
        return $this->namespaces;
    }
    /**
     * @return mixed[]
     */
    public function getCommands()
    {
        if (null === $this->commands) {
            $this->inspectApplication();
        }
        return $this->commands;
    }
    /**
     * @throws CommandNotFoundException
     * @param string $name
     * @return \Symfony\Component\Console\Command\Command
     */
    public function getCommand($name)
    {
        $name = (string) $name;
        if (!isset($this->commands[$name]) && !isset($this->aliases[$name])) {
            throw new \ECSPrefix20210515\Symfony\Component\Console\Exception\CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }
        return isset($this->commands[$name]) ? $this->commands[$name] : $this->aliases[$name];
    }
    private function inspectApplication()
    {
        $this->commands = [];
        $this->namespaces = [];
        $all = $this->application->all($this->namespace ? $this->application->findNamespace($this->namespace) : null);
        foreach ($this->sortCommands($all) as $namespace => $commands) {
            $names = [];
            /** @var Command $command */
            foreach ($commands as $name => $command) {
                if (!$command->getName() || !$this->showHidden && $command->isHidden()) {
                    continue;
                }
                if ($command->getName() === $name) {
                    $this->commands[$name] = $command;
                } else {
                    $this->aliases[$name] = $command;
                }
                $names[] = $name;
            }
            $this->namespaces[$namespace] = ['id' => $namespace, 'commands' => $names];
        }
    }
    /**
     * @return mixed[]
     */
    private function sortCommands(array $commands)
    {
        $namespacedCommands = [];
        $globalCommands = [];
        $sortedCommands = [];
        foreach ($commands as $name => $command) {
            $key = $this->application->extractNamespace($name, 1);
            if (\in_array($key, ['', self::GLOBAL_NAMESPACE], \true)) {
                $globalCommands[$name] = $command;
            } else {
                $namespacedCommands[$key][$name] = $command;
            }
        }
        if ($globalCommands) {
            \ksort($globalCommands);
            $sortedCommands[self::GLOBAL_NAMESPACE] = $globalCommands;
        }
        if ($namespacedCommands) {
            \ksort($namespacedCommands);
            foreach ($namespacedCommands as $key => $commandsSet) {
                \ksort($commandsSet);
                $sortedCommands[$key] = $commandsSet;
            }
        }
        return $sortedCommands;
    }
}
