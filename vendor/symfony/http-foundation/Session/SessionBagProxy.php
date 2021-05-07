<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpFoundation\Session;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class SessionBagProxy implements \ECSPrefix20210507\Symfony\Component\HttpFoundation\Session\SessionBagInterface
{
    private $bag;
    private $data;
    private $usageIndex;
    private $usageReporter;
    /**
     * @param int|null $usageIndex
     * @param callable|null $usageReporter
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Session\SessionBagInterface $bag
     */
    public function __construct($bag, array &$data, &$usageIndex, $usageReporter)
    {
        $this->bag = $bag;
        $this->data =& $data;
        $this->usageIndex =& $usageIndex;
        $this->usageReporter = $usageReporter;
    }
    /**
     * @return \ECSPrefix20210507\Symfony\Component\HttpFoundation\Session\SessionBagInterface
     */
    public function getBag()
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return $this->bag;
    }
    /**
     * @return bool
     */
    public function isEmpty()
    {
        if (!isset($this->data[$this->bag->getStorageKey()])) {
            return \true;
        }
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        return empty($this->data[$this->bag->getStorageKey()]);
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getName()
    {
        return $this->bag->getName();
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    public function initialize(array &$array)
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }
        $this->data[$this->bag->getStorageKey()] =& $array;
        $this->bag->initialize($array);
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getStorageKey()
    {
        return $this->bag->getStorageKey();
    }
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->bag->clear();
    }
}
