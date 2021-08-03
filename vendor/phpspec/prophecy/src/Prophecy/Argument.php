<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy;

use ECSPrefix20210803\Prophecy\Argument\Token;
/**
 * Argument tokens shortcuts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Argument
{
    /**
     * Checks that argument is exact value or object.
     *
     * @param mixed $value
     *
     * @return Token\ExactValueToken
     */
    public static function exact($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ExactValueToken($value);
    }
    /**
     * Checks that argument is of specific type or instance of specific class.
     *
     * @param string $type Type name (`integer`, `string`) or full class name
     *
     * @return Token\TypeToken
     */
    public static function type($type)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\TypeToken($type);
    }
    /**
     * Checks that argument object has specific state.
     *
     * @param string $methodName
     * @param mixed  $value
     *
     * @return Token\ObjectStateToken
     */
    public static function which($methodName, $value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ObjectStateToken($methodName, $value);
    }
    /**
     * Checks that argument matches provided callback.
     *
     * @param callable $callback
     *
     * @return Token\CallbackToken
     */
    public static function that($callback)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\CallbackToken($callback);
    }
    /**
     * Matches any single value.
     *
     * @return Token\AnyValueToken
     */
    public static function any()
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\AnyValueToken();
    }
    /**
     * Matches all values to the rest of the signature.
     *
     * @return Token\AnyValuesToken
     */
    public static function cetera()
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\AnyValuesToken();
    }
    /**
     * Checks that argument matches all tokens
     *
     * @param mixed ... a list of tokens
     *
     * @return Token\LogicalAndToken
     */
    public static function allOf()
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\LogicalAndToken(\func_get_args());
    }
    /**
     * Checks that argument array or countable object has exact number of elements.
     *
     * @param integer $value array elements count
     *
     * @return Token\ArrayCountToken
     */
    public static function size($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ArrayCountToken($value);
    }
    /**
     * Checks that argument array contains (key, value) pair
     *
     * @param mixed $key   exact value or token
     * @param mixed $value exact value or token
     *
     * @return Token\ArrayEntryToken
     */
    public static function withEntry($key, $value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ArrayEntryToken($key, $value);
    }
    /**
     * Checks that arguments array entries all match value
     *
     * @param mixed $value
     *
     * @return Token\ArrayEveryEntryToken
     */
    public static function withEveryEntry($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ArrayEveryEntryToken($value);
    }
    /**
     * Checks that argument array contains value
     *
     * @param mixed $value
     *
     * @return Token\ArrayEntryToken
     */
    public static function containing($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ArrayEntryToken(self::any(), $value);
    }
    /**
     * Checks that argument array has key
     *
     * @param mixed $key exact value or token
     *
     * @return Token\ArrayEntryToken
     */
    public static function withKey($key)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ArrayEntryToken($key, self::any());
    }
    /**
     * Checks that argument does not match the value|token.
     *
     * @param mixed $value either exact value or argument token
     *
     * @return Token\LogicalNotToken
     */
    public static function not($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\LogicalNotToken($value);
    }
    /**
     * @param string $value
     *
     * @return Token\StringContainsToken
     */
    public static function containingString($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\StringContainsToken($value);
    }
    /**
     * Checks that argument is identical value.
     *
     * @param mixed $value
     *
     * @return Token\IdenticalValueToken
     */
    public static function is($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\IdenticalValueToken($value);
    }
    /**
     * Check that argument is same value when rounding to the
     * given precision.
     *
     * @param float $value
     * @param float $precision
     *
     * @return Token\ApproximateValueToken
     */
    public static function approximate($value, $precision = 0)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\ApproximateValueToken($value, $precision);
    }
    /**
     * Checks that argument is in array.
     *
     * @param array $value
     *
     * @return Token\InArrayToken
     */
    public static function in($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\InArrayToken($value);
    }
    /**
     * Checks that argument is not in array.
     *
     * @param array $value
     *
     * @return Token\NotInArrayToken
     */
    public static function notIn($value)
    {
        return new \ECSPrefix20210803\Prophecy\Argument\Token\NotInArrayToken($value);
    }
}
