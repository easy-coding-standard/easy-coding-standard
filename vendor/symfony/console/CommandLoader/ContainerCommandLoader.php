<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220220\Symfony\Component\Console\CommandLoader;

use ECSPrefix20220220\Psr\Container\ContainerInterface;
use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Exception\CommandNotFoundException;
/**
 * Loads commands from a PSR-11 container.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class ContainerCommandLoader implements \ECSPrefix20220220\Symfony\Component\Console\CommandLoader\CommandLoaderInterface
{
    private $container;
    /**
     * @var mixed[]
     */
    private $commandMap;
    /**
     * @param array $commandMap An array with command names as keys and service ids as values
     */
    public function __construct(\ECSPrefix20220220\Psr\Container\ContainerInterface $container, array $commandMap)
    {
        $this->container = $container;
        $this->commandMap = $commandMap;
    }
    /**
     * {@inheritdoc}
     */
    public function get(string $name) : \ECSPrefix20220220\Symfony\Component\Console\Command\Command
    {
        if (!$this->has($name)) {
            throw new \ECSPrefix20220220\Symfony\Component\Console\Exception\CommandNotFoundException(\sprintf('Command "%s" does not exist.', $name));
        }
        return $this->container->get($this->commandMap[$name]);
    }
    /**
     * {@inheritdoc}
     */
    public function has(string $name) : bool
    {
        return isset($this->commandMap[$name]) && $this->container->has($this->commandMap[$name]);
    }
    /**
     * {@inheritdoc}
     */
    public function getNames() : array
    {
        return \array_keys($this->commandMap);
    }
}
