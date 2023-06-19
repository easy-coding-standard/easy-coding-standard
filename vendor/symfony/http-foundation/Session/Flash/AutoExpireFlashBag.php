<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Flash;

/**
 * AutoExpireFlashBag flash message container.
 *
 * @author Drak <drak@zikula.org>
 */
class AutoExpireFlashBag implements FlashBagInterface
{
    /**
     * @var string
     */
    private $name = 'flashes';
    /**
     * @var mixed[]
     */
    private $flashes = ['display' => [], 'new' => []];
    /**
     * @var string
     */
    private $storageKey;
    /**
     * @param string $storageKey The key used to store flashes in the session
     */
    public function __construct(string $storageKey = '_symfony_flashes')
    {
        $this->storageKey = $storageKey;
    }
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    /**
     * @return void
     */
    public function initialize(array &$flashes)
    {
        $this->flashes =& $flashes;
        // The logic: messages from the last request will be stored in new, so we move them to previous
        // This request we will show what is in 'display'.  What is placed into 'new' this time round will
        // be moved to display next time round.
        $this->flashes['display'] = \array_key_exists('new', $this->flashes) ? $this->flashes['new'] : [];
        $this->flashes['new'] = [];
    }
    /**
     * @return void
     * @param mixed $message
     */
    public function add(string $type, $message)
    {
        $this->flashes['new'][$type][] = $message;
    }
    public function peek(string $type, array $default = []) : array
    {
        return $this->has($type) ? $this->flashes['display'][$type] : $default;
    }
    public function peekAll() : array
    {
        return \array_key_exists('display', $this->flashes) ? $this->flashes['display'] : [];
    }
    public function get(string $type, array $default = []) : array
    {
        $return = $default;
        if (!$this->has($type)) {
            return $return;
        }
        if (isset($this->flashes['display'][$type])) {
            $return = $this->flashes['display'][$type];
            unset($this->flashes['display'][$type]);
        }
        return $return;
    }
    public function all() : array
    {
        $return = $this->flashes['display'];
        $this->flashes['display'] = [];
        return $return;
    }
    /**
     * @return void
     */
    public function setAll(array $messages)
    {
        $this->flashes['new'] = $messages;
    }
    /**
     * @return void
     * @param string|mixed[] $messages
     */
    public function set(string $type, $messages)
    {
        $this->flashes['new'][$type] = (array) $messages;
    }
    public function has(string $type) : bool
    {
        return \array_key_exists($type, $this->flashes['display']) && $this->flashes['display'][$type];
    }
    public function keys() : array
    {
        return \array_keys($this->flashes['display']);
    }
    public function getStorageKey() : string
    {
        return $this->storageKey;
    }
    /**
     * @return mixed
     */
    public function clear()
    {
        return $this->all();
    }
}
