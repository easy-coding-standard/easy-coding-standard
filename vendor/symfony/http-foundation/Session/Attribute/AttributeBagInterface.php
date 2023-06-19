<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Attribute;

use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\SessionBagInterface;
/**
 * Attributes store.
 *
 * @author Drak <drak@zikula.org>
 */
interface AttributeBagInterface extends SessionBagInterface
{
    /**
     * Checks if an attribute is defined.
     */
    public function has(string $name) : bool;
    /**
     * Returns an attribute.
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null);
    /**
     * Sets an attribute.
     *
     * @return void
     * @param mixed $value
     */
    public function set(string $name, $value);
    /**
     * Returns attributes.
     *
     * @return array<string, mixed>
     */
    public function all() : array;
    /**
     * @return void
     */
    public function replace(array $attributes);
    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove(string $name);
}
