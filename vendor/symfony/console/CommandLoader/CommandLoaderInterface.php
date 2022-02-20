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

use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Exception\CommandNotFoundException;
/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
interface CommandLoaderInterface
{
    /**
     * Loads a command.
     *
     * @throws CommandNotFoundException
     */
    public function get(string $name) : \ECSPrefix20220220\Symfony\Component\Console\Command\Command;
    /**
     * Checks if a command exists.
     */
    public function has(string $name) : bool;
    /**
     * @return string[]
     */
    public function getNames() : array;
}
