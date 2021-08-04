<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\Comparator;

use function array_unshift;
/**
 * Factory for comparators which compare values for equality.
 */
class Factory
{
    /**
     * @var Factory
     */
    private static $instance;
    /**
     * @var Comparator[]
     */
    private $customComparators = [];
    /**
     * @var Comparator[]
     */
    private $defaultComparators = [];
    /**
     * @return Factory
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
            // @codeCoverageIgnore
        }
        return self::$instance;
    }
    /**
     * Constructs a new factory.
     */
    public function __construct()
    {
        $this->registerDefaultComparators();
    }
    /**
     * Returns the correct comparator for comparing two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return Comparator
     */
    public function getComparatorFor($expected, $actual)
    {
        foreach ($this->customComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }
        foreach ($this->defaultComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }
        throw new \ECSPrefix20210804\SebastianBergmann\Comparator\RuntimeException('No suitable Comparator implementation found');
    }
    /**
     * Registers a new comparator.
     *
     * This comparator will be returned by getComparatorFor() if its accept() method
     * returns TRUE for the compared values. It has higher priority than the
     * existing comparators, meaning that its accept() method will be invoked
     * before those of the other comparators.
     *
     * @param Comparator $comparator The comparator to be registered
     */
    public function register($comparator)
    {
        \array_unshift($this->customComparators, $comparator);
        $comparator->setFactory($this);
    }
    /**
     * Unregisters a comparator.
     *
     * This comparator will no longer be considered by getComparatorFor().
     *
     * @param Comparator $comparator The comparator to be unregistered
     */
    public function unregister($comparator)
    {
        foreach ($this->customComparators as $key => $_comparator) {
            if ($comparator === $_comparator) {
                unset($this->customComparators[$key]);
            }
        }
    }
    /**
     * Unregisters all non-default comparators.
     */
    public function reset()
    {
        $this->customComparators = [];
    }
    /**
     * @return void
     */
    private function registerDefaultComparators()
    {
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\MockObjectComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\DateTimeComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\DOMNodeComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\SplObjectStorageComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\ExceptionComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\ObjectComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\ResourceComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\ArrayComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\DoubleComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\NumericComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\ScalarComparator());
        $this->registerDefaultComparator(new \ECSPrefix20210804\SebastianBergmann\Comparator\TypeComparator());
    }
    /**
     * @return void
     */
    private function registerDefaultComparator(\ECSPrefix20210804\SebastianBergmann\Comparator\Comparator $comparator)
    {
        $this->defaultComparators[] = $comparator;
        $comparator->setFactory($this);
    }
}
