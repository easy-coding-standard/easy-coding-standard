<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\HttpFoundation\Session\Flash;

/**
 * FlashBag flash message container.
 *
 * @author Drak <drak@zikula.org>
 */
class FlashBag implements \ECSPrefix20210510\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
{
    private $name = 'flashes';
    private $flashes = [];
    private $storageKey;
    /**
     * @param string $storageKey The key used to store flashes in the session
     */
    public function __construct($storageKey = '_symfony_flashes')
    {
        $storageKey = (string) $storageKey;
        $this->storageKey = $storageKey;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $name = (string) $name;
        $this->name = $name;
    }
    /**
     * {@inheritdoc}
     */
    public function initialize(array &$flashes)
    {
        $this->flashes =& $flashes;
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function add($type, $message)
    {
        $type = (string) $type;
        $this->flashes[$type][] = $message;
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function peek($type, array $default = [])
    {
        $type = (string) $type;
        return $this->has($type) ? $this->flashes[$type] : $default;
    }
    /**
     * {@inheritdoc}
     */
    public function peekAll()
    {
        return $this->flashes;
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function get($type, array $default = [])
    {
        $type = (string) $type;
        if (!$this->has($type)) {
            return $default;
        }
        $return = $this->flashes[$type];
        unset($this->flashes[$type]);
        return $return;
    }
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $return = $this->peekAll();
        $this->flashes = [];
        return $return;
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function set($type, $messages)
    {
        $type = (string) $type;
        $this->flashes[$type] = (array) $messages;
    }
    /**
     * {@inheritdoc}
     */
    public function setAll(array $messages)
    {
        $this->flashes = $messages;
    }
    /**
     * {@inheritdoc}
     * @param string $type
     */
    public function has($type)
    {
        $type = (string) $type;
        return \array_key_exists($type, $this->flashes) && $this->flashes[$type];
    }
    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return \array_keys($this->flashes);
    }
    /**
     * {@inheritdoc}
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->all();
    }
}
