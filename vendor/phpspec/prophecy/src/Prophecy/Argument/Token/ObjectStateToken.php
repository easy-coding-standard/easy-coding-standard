<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Argument\Token;

use ECSPrefix20210804\SebastianBergmann\Comparator\ComparisonFailure;
use ECSPrefix20210804\Prophecy\Comparator\Factory as ComparatorFactory;
use ECSPrefix20210804\Prophecy\Util\StringUtil;
/**
 * Object state-checker token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ObjectStateToken implements \ECSPrefix20210804\Prophecy\Argument\Token\TokenInterface
{
    private $name;
    private $value;
    private $util;
    private $comparatorFactory;
    /**
     * Initializes token.
     *
     * @param string            $methodName
     * @param mixed             $value             Expected return value
     * @param null|StringUtil   $util
     * @param ComparatorFactory $comparatorFactory
     */
    public function __construct($methodName, $value, \ECSPrefix20210804\Prophecy\Util\StringUtil $util = null, \ECSPrefix20210804\Prophecy\Comparator\Factory $comparatorFactory = null)
    {
        $this->name = $methodName;
        $this->value = $value;
        $this->util = $util ?: new \ECSPrefix20210804\Prophecy\Util\StringUtil();
        $this->comparatorFactory = $comparatorFactory ?: \ECSPrefix20210804\Prophecy\Comparator\Factory::getInstance();
    }
    /**
     * Scores 8 if argument is an object, which method returns expected value.
     *
     * @param mixed $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (\is_object($argument) && \method_exists($argument, $this->name)) {
            $actual = \call_user_func(array($argument, $this->name));
            $comparator = $this->comparatorFactory->getComparatorFor($this->value, $actual);
            try {
                $comparator->assertEquals($this->value, $actual);
                return 8;
            } catch (\ECSPrefix20210804\SebastianBergmann\Comparator\ComparisonFailure $failure) {
                return \false;
            }
        }
        if (\is_object($argument) && \property_exists($argument, $this->name)) {
            return $argument->{$this->name} === $this->value ? 8 : \false;
        }
        return \false;
    }
    /**
     * Returns false.
     *
     * @return bool
     */
    public function isLast()
    {
        return \false;
    }
    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        return \sprintf('state(%s(), %s)', $this->name, $this->util->stringify($this->value));
    }
}
