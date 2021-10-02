<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash;

use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagInterface;
/**
 * FlashBagInterface.
 *
 * @author Drak <drak@zikula.org>
 */
interface FlashBagInterface extends \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagInterface
{
    /**
     * Adds a flash message for the given type.
     *
     * @param mixed $message
     * @param string $type
     */
    public function add($type, $message);
    /**
     * Registers one or more messages for a given type.
     *
     * @param string|array $messages
     * @param string $type
     */
    public function set($type, $messages);
    /**
     * Gets flash messages for a given type.
     *
     * @param string $type    Message category type
     * @param array  $default Default value if $type does not exist
     *
     * @return array
     */
    public function peek($type, $default = []);
    /**
     * Gets all flash messages.
     *
     * @return array
     */
    public function peekAll();
    /**
     * Gets and clears flash from the stack.
     *
     * @param array $default Default value if $type does not exist
     *
     * @return array
     * @param string $type
     */
    public function get($type, $default = []);
    /**
     * Gets and clears flashes from the stack.
     *
     * @return array
     */
    public function all();
    /**
     * Sets all flash messages.
     * @param mixed[] $messages
     */
    public function setAll($messages);
    /**
     * Has flash messages for a given type?
     *
     * @return bool
     * @param string $type
     */
    public function has($type);
    /**
     * Returns a list of all defined types.
     *
     * @return array
     */
    public function keys();
}
