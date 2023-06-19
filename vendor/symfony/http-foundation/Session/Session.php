<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Session;

use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(AttributeBag::class);
\class_exists(FlashBag::class);
\class_exists(SessionBagProxy::class);
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Drak <drak@zikula.org>
 *
 * @implements \IteratorAggregate<string, mixed>
 */
class Session implements FlashBagAwareSessionInterface, \IteratorAggregate, \Countable
{
    protected $storage;
    /**
     * @var string
     */
    private $flashName;
    /**
     * @var string
     */
    private $attributeName;
    /**
     * @var mixed[]
     */
    private $data = [];
    /**
     * @var int
     */
    private $usageIndex = 0;
    /**
     * @var \Closure|null
     */
    private $usageReporter;
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null, callable $usageReporter = null)
    {
        $this->storage = $storage ?? new NativeSessionStorage();
        $this->usageReporter = null === $usageReporter ? null : \Closure::fromCallable($usageReporter);
        $attributes = $attributes ?? new AttributeBag();
        $this->attributeName = $attributes->getName();
        $this->registerBag($attributes);
        $flashes = $flashes ?? new FlashBag();
        $this->flashName = $flashes->getName();
        $this->registerBag($flashes);
    }
    public function start() : bool
    {
        return $this->storage->start();
    }
    public function has(string $name) : bool
    {
        return $this->getAttributeBag()->has($name);
    }
    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->getAttributeBag()->get($name, $default);
    }
    /**
     * @return void
     * @param mixed $value
     */
    public function set(string $name, $value)
    {
        $this->getAttributeBag()->set($name, $value);
    }
    public function all() : array
    {
        return $this->getAttributeBag()->all();
    }
    /**
     * @return void
     */
    public function replace(array $attributes)
    {
        $this->getAttributeBag()->replace($attributes);
    }
    /**
     * @return mixed
     */
    public function remove(string $name)
    {
        return $this->getAttributeBag()->remove($name);
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->getAttributeBag()->clear();
    }
    public function isStarted() : bool
    {
        return $this->storage->isStarted();
    }
    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->getAttributeBag()->all());
    }
    /**
     * Returns the number of attributes.
     */
    public function count() : int
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
    public function invalidate(int $lifetime = null) : bool
    {
        $this->storage->clear();
        return $this->migrate(\true, $lifetime);
    }
    public function migrate(bool $destroy = \false, int $lifetime = null) : bool
    {
        return $this->storage->regenerate($destroy, $lifetime);
    }
    /**
     * @return void
     */
    public function save()
    {
        $this->storage->save();
    }
    public function getId() : string
    {
        return $this->storage->getId();
    }
    /**
     * @return void
     */
    public function setId(string $id)
    {
        if ($this->storage->getId() !== $id) {
            $this->storage->setId($id);
        }
    }
    public function getName() : string
    {
        return $this->storage->getName();
    }
    /**
     * @return void
     */
    public function setName(string $name)
    {
        $this->storage->setName($name);
    }
    public function getMetadataBag() : MetadataBag
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return $this->storage->getMetadataBag();
    }
    /**
     * @return void
     */
    public function registerBag(SessionBagInterface $bag)
    {
        $this->storage->registerBag(new SessionBagProxy($bag, $this->data, $this->usageIndex, $this->usageReporter));
    }
    public function getBag(string $name) : SessionBagInterface
    {
        $bag = $this->storage->getBag($name);
        return \method_exists($bag, 'getBag') ? $bag->getBag() : $bag;
    }
    /**
     * Gets the flashbag interface.
     */
    public function getFlashBag() : FlashBagInterface
    {
        return $this->getBag($this->flashName);
    }
    /**
     * Gets the attributebag interface.
     *
     * Note that this method was added to help with IDE autocompletion.
     */
    private function getAttributeBag() : AttributeBagInterface
    {
        return $this->getBag($this->attributeName);
    }
}
