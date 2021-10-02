<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session;

use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag::class);
\class_exists(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash\FlashBag::class);
\class_exists(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagProxy::class);
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Drak <drak@zikula.org>
 */
class Session implements \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionInterface, \IteratorAggregate, \Countable
{
    protected $storage;
    private $flashName;
    private $attributeName;
    private $data = [];
    private $usageIndex = 0;
    private $usageReporter;
    public function __construct(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface $storage = null, \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $attributes = null, \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashes = null, callable $usageReporter = null)
    {
        $this->storage = $storage ?? new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage();
        $this->usageReporter = $usageReporter;
        $attributes = $attributes ?? new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag();
        $this->attributeName = $attributes->getName();
        $this->registerBag($attributes);
        $flashes = $flashes ?? new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Flash\FlashBag();
        $this->flashName = $flashes->getName();
        $this->registerBag($flashes);
    }
    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this->storage->start();
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function has($name)
    {
        return $this->getAttributeBag()->has($name);
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function get($name, $default = null)
    {
        return $this->getAttributeBag()->get($name, $default);
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function set($name, $value)
    {
        $this->getAttributeBag()->set($name, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->getAttributeBag()->all();
    }
    /**
     * {@inheritdoc}
     * @param mixed[] $attributes
     */
    public function replace($attributes)
    {
        $this->getAttributeBag()->replace($attributes);
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function remove($name)
    {
        return $this->getAttributeBag()->remove($name);
    }
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->getAttributeBag()->clear();
    }
    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->storage->isStarted();
    }
    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->getAttributeBag()->all());
    }
    /**
     * Returns the number of attributes.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return \count($this->getAttributeBag()->all());
    }
    public function &getUsageIndex() : int
    {
        return $this->usageIndex;
    }
    /**
     * @internal
     */
    public function isEmpty() : bool
    {
        if ($this->isStarted()) {
            ++$this->usageIndex;
            if ($this->usageReporter && 0 <= $this->usageIndex) {
                ($this->usageReporter)();
            }
        }
        foreach ($this->data as &$data) {
            if (!empty($data)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * {@inheritdoc}
     * @param int|null $lifetime
     */
    public function invalidate($lifetime = null)
    {
        $this->storage->clear();
        return $this->migrate(\true, $lifetime);
    }
    /**
     * {@inheritdoc}
     * @param bool $destroy
     * @param int|null $lifetime
     */
    public function migrate($destroy = \false, $lifetime = null)
    {
        return $this->storage->regenerate($destroy, $lifetime);
    }
    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->storage->save();
    }
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->storage->getId();
    }
    /**
     * {@inheritdoc}
     * @param string $id
     */
    public function setId($id)
    {
        if ($this->storage->getId() !== $id) {
            $this->storage->setId($id);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->storage->getName();
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function setName($name)
    {
        $this->storage->setName($name);
    }
    /**
     * {@inheritdoc}
     */
    public function getMetadataBag()
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return $this->storage->getMetadataBag();
    }
    /**
     * {@inheritdoc}
     * @param \Symfony\Component\HttpFoundation\Session\SessionBagInterface $bag
     */
    public function registerBag($bag)
    {
        $this->storage->registerBag(new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\SessionBagProxy($bag, $this->data, $this->usageIndex, $this->usageReporter));
    }
    /**
     * {@inheritdoc}
     * @param string $name
     */
    public function getBag($name)
    {
        $bag = $this->storage->getBag($name);
        return \method_exists($bag, 'getBag') ? $bag->getBag() : $bag;
    }
    /**
     * Gets the flashbag interface.
     *
     * @return FlashBagInterface
     */
    public function getFlashBag()
    {
        return $this->getBag($this->flashName);
    }
    /**
     * Gets the attributebag interface.
     *
     * Note that this method was added to help with IDE autocompletion.
     */
    private function getAttributeBag() : \ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface
    {
        return $this->getBag($this->attributeName);
    }
}
