<?php

namespace ECSPrefix20210507\Nette\Utils;

use ECSPrefix20210507\Nette;
/**
 * Validation utilities.
 */
class Validators
{
    use Nette\StaticClass;
    /** @var array<string,?callable> */
    protected static $validators = [
        // PHP types
        'array' => 'is_array',
        'bool' => 'is_bool',
        'boolean' => 'is_bool',
        'float' => 'is_float',
        'int' => 'is_int',
        'integer' => 'is_int',
        'null' => 'is_null',
        'object' => 'is_object',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
        // pseudo-types
        'callable' => [self::class, 'isCallable'],
        'iterable' => 'is_iterable',
        'list' => [\ECSPrefix20210507\Nette\Utils\Arrays::class, 'isList'],
        'mixed' => [self::class, 'isMixed'],
        'none' => [self::class, 'isNone'],
        'number' => [self::class, 'isNumber'],
        'numeric' => [self::class, 'isNumeric'],
        'numericint' => [self::class, 'isNumericInt'],
        // string patterns
        'alnum' => 'ctype_alnum',
        'alpha' => 'ctype_alpha',
        'digit' => 'ctype_digit',
        'lower' => 'ctype_lower',
        'pattern' => null,
        'space' => 'ctype_space',
        'unicode' => [self::class, 'isUnicode'],
        'upper' => 'ctype_upper',
        'xdigit' => 'ctype_xdigit',
        // syntax validation
        'email' => [self::class, 'isEmail'],
        'identifier' => [self::class, 'isPhpIdentifier'],
        'uri' => [self::class, 'isUri'],
        'url' => [self::class, 'isUrl'],
        // environment validation
        'class' => 'class_exists',
        'interface' => 'interface_exists',
        'directory' => 'is_dir',
        'file' => 'is_file',
        'type' => [self::class, 'isType'],
    ];
    /** @var array<string,callable> */
    protected static $counters = ['string' => 'strlen', 'unicode' => [\ECSPrefix20210507\Nette\Utils\Strings::class, 'length'], 'array' => 'count', 'list' => 'count', 'alnum' => 'strlen', 'alpha' => 'strlen', 'digit' => 'strlen', 'lower' => 'strlen', 'space' => 'strlen', 'upper' => 'strlen', 'xdigit' => 'strlen'];
    /**
     * Verifies that the value is of expected types separated by pipe.
     * @param  mixed  $value
     * @throws AssertionException
     * @return void
     * @param string $expected
     * @param string $label
     */
    public static function assert($value, $expected, $label = 'variable')
    {
        if (!static::is($value, $expected)) {
            $expected = \str_replace(['|', ':'], [' or ', ' in range '], $expected);
            static $translate = ['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'NULL' => 'null'];
            $type = isset($translate[\gettype($value)]) ? $translate[\gettype($value)] : \gettype($value);
            if (\is_int($value) || \is_float($value) || \is_string($value) && \strlen($value) < 40) {
                $type .= ' ' . \var_export($value, \true);
            } elseif (\is_object($value)) {
                $type .= ' ' . \get_class($value);
            }
            throw new \ECSPrefix20210507\Nette\Utils\AssertionException("The {$label} expects to be {$expected}, {$type} given.");
        }
    }
    /**
     * Verifies that element $key in array is of expected types separated by pipe.
     * @param  mixed[]  $array
     * @param  int|string  $key
     * @throws AssertionException
     * @return void
     * @param string $expected
     * @param string $label
     */
    public static function assertField(array $array, $key, $expected = null, $label = "item '%' in array")
    {
        if (!\array_key_exists($key, $array)) {
            throw new \ECSPrefix20210507\Nette\Utils\AssertionException('Missing ' . \str_replace('%', $key, $label) . '.');
        } elseif ($expected) {
            static::assert($array[$key], $expected, \str_replace('%', $key, $label));
        }
    }
    /**
     * Verifies that the value is of expected types separated by pipe.
     * @param  mixed  $value
     * @param string $expected
     * @return bool
     */
    public static function is($value, $expected)
    {
        foreach (\explode('|', $expected) as $item) {
            if (\substr($item, -2) === '[]') {
                if ((is_array($value) || $value instanceof \Traversable) && self::everyIs($value, \substr($item, 0, -2))) {
                    return \true;
                }
                continue;
            } elseif (\substr($item, 0, 1) === '?') {
                $item = \substr($item, 1);
                if ($value === null) {
                    return \true;
                }
            }
            list($type) = $item = \explode(':', $item, 2);
            if (isset(static::$validators[$type])) {
                try {
                    if (!static::$validators[$type]($value)) {
                        continue;
                    }
                } catch (\TypeError $e) {
                    continue;
                }
            } elseif ($type === 'pattern') {
                if (\ECSPrefix20210507\Nette\Utils\Strings::match($value, '|^' . (isset($item[1]) ? $item[1] : '') . '$|D')) {
                    return \true;
                }
                continue;
            } elseif (!$value instanceof $type) {
                continue;
            }
            if (isset($item[1])) {
                $length = $value;
                if (isset(static::$counters[$type])) {
                    $length = static::$counters[$type]($value);
                }
                $range = \explode('..', $item[1]);
                if (!isset($range[1])) {
                    $range[1] = $range[0];
                }
                if ($range[0] !== '' && $length < $range[0] || $range[1] !== '' && $length > $range[1]) {
                    continue;
                }
            }
            return \true;
        }
        return \false;
    }
    /**
     * Finds whether all values are of expected types separated by pipe.
     * @param  mixed[]  $values
     * @param string $expected
     * @return bool
     */
    public static function everyIs($values, $expected)
    {
        foreach ($values as $value) {
            if (!static::is($value, $expected)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Checks if the value is an integer or a float.
     * @param  mixed  $value
     * @return bool
     */
    public static function isNumber($value)
    {
        return \is_int($value) || \is_float($value);
    }
    /**
     * Checks if the value is an integer or a integer written in a string.
     * @param  mixed  $value
     * @return bool
     */
    public static function isNumericInt($value)
    {
        return \is_int($value) || \is_string($value) && \preg_match('#^[+-]?[0-9]+$#D', $value);
    }
    /**
     * Checks if the value is a number or a number written in a string.
     * @param  mixed  $value
     * @return bool
     */
    public static function isNumeric($value)
    {
        return \is_float($value) || \is_int($value) || \is_string($value) && \preg_match('#^[+-]?[0-9]*[.]?[0-9]+$#D', $value);
    }
    /**
     * Checks if the value is a syntactically correct callback.
     * @param  mixed  $value
     * @return bool
     */
    public static function isCallable($value)
    {
        return $value && \is_callable($value, \true);
    }
    /**
     * Checks if the value is a valid UTF-8 string.
     * @param  mixed  $value
     * @return bool
     */
    public static function isUnicode($value)
    {
        return \is_string($value) && \preg_match('##u', $value);
    }
    /**
     * Checks if the value is 0, '', false or null.
     * @param  mixed  $value
     * @return bool
     */
    public static function isNone($value)
    {
        return $value == null;
        // intentionally ==
    }
    /** @internal
     * @return bool */
    public static function isMixed()
    {
        return \true;
    }
    /**
     * Checks if a variable is a zero-based integer indexed array.
     * @param  mixed  $value
     * @deprecated  use Nette\Utils\Arrays::isList
     * @return bool
     */
    public static function isList($value)
    {
        return \ECSPrefix20210507\Nette\Utils\Arrays::isList($value);
    }
    /**
     * Checks if the value is in the given range [min, max], where the upper or lower limit can be omitted (null).
     * Numbers, strings and DateTime objects can be compared.
     * @param  mixed  $value
     * @return bool
     */
    public static function isInRange($value, array $range)
    {
        if ($value === null || !(isset($range[0]) || isset($range[1]))) {
            return \false;
        }
        $limit = isset($range[0]) ? $range[0] : $range[1];
        if (\is_string($limit)) {
            $value = (string) $value;
        } elseif ($limit instanceof \DateTimeInterface) {
            if (!$value instanceof \DateTimeInterface) {
                return \false;
            }
        } elseif (\is_numeric($value)) {
            $value *= 1;
        } else {
            return \false;
        }
        return (!isset($range[0]) || $value >= $range[0]) && (!isset($range[1]) || $value <= $range[1]);
    }
    /**
     * Checks if the value is a valid email address. It does not verify that the domain actually exists, only the syntax is verified.
     * @param string $value
     * @return bool
     */
    public static function isEmail($value)
    {
        $atom = "[-a-z0-9!#\$%&'*+/=?^_`{|}~]";
        // RFC 5322 unquoted characters in local-part
        $alpha = "a-z€-ÿ";
        // superset of IDN
        return (bool) \preg_match(<<<XX
\t\t(^
\t\t\t("([ !#-[\\]-~]*|\\\\[ -~])+"|{$atom}+(\\.{$atom}+)*)  # quoted or unquoted
\t\t\t@
\t\t\t([0-9{$alpha}]([-0-9{$alpha}]{0,61}[0-9{$alpha}])?\\.)+  # domain - RFC 1034
\t\t\t[{$alpha}]([-0-9{$alpha}]{0,17}[{$alpha}])?              # top domain
\t\t\$)Dix
XX
, $value);
    }
    /**
     * Checks if the value is a valid URL address.
     * @param string $value
     * @return bool
     */
    public static function isUrl($value)
    {
        $alpha = "a-z€-ÿ";
        return (bool) \preg_match(<<<XX
\t\t(^
\t\t\thttps?://(
\t\t\t\t(([-_0-9{$alpha}]+\\.)*                       # subdomain
\t\t\t\t\t[0-9{$alpha}]([-0-9{$alpha}]{0,61}[0-9{$alpha}])?\\.)?  # domain
\t\t\t\t\t[{$alpha}]([-0-9{$alpha}]{0,17}[{$alpha}])?   # top domain
\t\t\t\t|\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}  # IPv4
\t\t\t\t|\\[[0-9a-f:]{3,39}\\]                      # IPv6
\t\t\t)(:\\d{1,5})?                                   # port
\t\t\t(/\\S*)?                                        # path
\t\t\t(\\?\\S*)?                                      # query
\t\t\t(\\#\\S*)?                                      # fragment
\t\t\$)Dix
XX
, $value);
    }
    /**
     * Checks if the value is a valid URI address, that is, actually a string beginning with a syntactically valid schema.
     * @param string $value
     * @return bool
     */
    public static function isUri($value)
    {
        return (bool) \preg_match('#^[a-z\\d+\\.-]+:\\S+$#Di', $value);
    }
    /**
     * Checks whether the input is a class, interface or trait.
     * @param string $type
     * @return bool
     */
    public static function isType($type)
    {
        return \class_exists($type) || \interface_exists($type) || \trait_exists($type);
    }
    /**
     * Checks whether the input is a valid PHP identifier.
     * @param string $value
     * @return bool
     */
    public static function isPhpIdentifier($value)
    {
        return \is_string($value) && \preg_match('#^[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*$#D', $value);
    }
}
