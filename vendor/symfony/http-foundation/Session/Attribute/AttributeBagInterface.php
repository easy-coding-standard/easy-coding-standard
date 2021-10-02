<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute;

use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagInterface;
/**
 * Attributes store.
 *
 * @author Drak <drak@zikula.org>
 */
interface AttributeBagInterface extends \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagInterface
{
    /**
     * Checks if an attribute is defined.
     *
     * @return bool true if the attribute is defined, false otherwise
     * @param string $name
     */
    public function has($name);
    /**
     * Returns an attribute.
     *
     * @param mixed $default The default value if not found
     *
     * @return mixed
     * @param string $name
     */
    public function get($name, $default = null);
    /**
     * Sets an attribute.
     *
     * @param mixed $value
     * @param string $name
     */
    public function set($name, $value);
    /**
     * Returns attributes.
     *
     * @return array
     */
    public function all();
    /**
     * @param mixed[] $attributes
     */
    public function replace($attributes);
    /**
     * Removes an attribute.
     *
     * @return mixed The removed value or null when it does not exist
     * @param string $name
     */
    public function remove($name);
}
