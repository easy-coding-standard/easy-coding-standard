<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

use Symfony\Component\String\Exception\ExceptionInterface;
use Symfony\Component\String\Exception\InvalidArgumentException;
use Symfony\Component\String\Exception\RuntimeException;

/**
 * Represents a string of abstract characters.
 *
 * Unicode defines 3 types of "characters" (bytes, code points and grapheme clusters).
 * This class is the abstract type to use as a type-hint when the logic you want to
 * implement doesn't care about the exact variant it deals with.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Hugo Hamon <hugohamon@neuf.fr>
 *
 * @throws ExceptionInterface
 */
abstract class AbstractString implements \Stringable, \JsonSerializable
{
    const PREG_PATTERN_ORDER = \PREG_PATTERN_ORDER;
    const PREG_SET_ORDER = \PREG_SET_ORDER;
    const PREG_OFFSET_CAPTURE = \PREG_OFFSET_CAPTURE;
    const PREG_UNMATCHED_AS_NULL = 512;

    const PREG_SPLIT = 0;
    const PREG_SPLIT_NO_EMPTY = \PREG_SPLIT_NO_EMPTY;
    const PREG_SPLIT_DELIM_CAPTURE = \PREG_SPLIT_DELIM_CAPTURE;
    const PREG_SPLIT_OFFSET_CAPTURE = \PREG_SPLIT_OFFSET_CAPTURE;

    protected $string = '';
    protected $ignoreCase = false;

    /**
     * @param string $string
     */
    abstract public function __construct($string = '');

    /**
     * Unwraps instances of AbstractString back to strings.
     *
     * @return mixed[]
     */
    public static function unwrap(array $values)
    {
        foreach ($values as $k => $v) {
            if ($v instanceof self) {
                $values[$k] = $v->__toString();
            } elseif (\is_array($v) && $values[$k] !== $v = static::unwrap($v)) {
                $values[$k] = $v;
            }
        }

        return $values;
    }

    /**
     * Wraps (and normalizes) strings in instances of AbstractString.
     *
     * @return mixed[]
     */
    public static function wrap(array $values)
    {
        $i = 0;
        $keys = null;

        foreach ($values as $k => $v) {
            if (\is_string($k) && '' !== $k && $k !== $j = (string) new static($k)) {
                $keys = isset($keys) ? $keys : array_keys($values);
                $keys[$i] = $j;
            }

            if (\is_string($v)) {
                $values[$k] = new static($v);
            } elseif (\is_array($v) && $values[$k] !== $v = static::wrap($v)) {
                $values[$k] = $v;
            }

            ++$i;
        }

        return null !== $keys ? array_combine($keys, $values) : $values;
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     * @param bool $includeNeedle
     * @param int $offset
     */
    public function after($needle, $includeNeedle = false, $offset = 0)
    {
        $includeNeedle = (bool) $includeNeedle;
        $offset = (int) $offset;
        $str = clone $this;
        $i = \PHP_INT_MAX;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOf($n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
                $str->string = $n;
            }
        }

        if (\PHP_INT_MAX === $i) {
            return $str;
        }

        if (!$includeNeedle) {
            $i += $str->length();
        }

        return $this->slice($i);
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     * @param bool $includeNeedle
     * @param int $offset
     */
    public function afterLast($needle, $includeNeedle = false, $offset = 0)
    {
        $includeNeedle = (bool) $includeNeedle;
        $offset = (int) $offset;
        $str = clone $this;
        $i = null;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOfLast($n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
                $str->string = $n;
            }
        }

        if (null === $i) {
            return $str;
        }

        if (!$includeNeedle) {
            $i += $str->length();
        }

        return $this->slice($i);
    }

    /**
     * @return static
     * @param string ...$suffix
     */
    abstract public function append(...$suffix);

    /**
     * @param string|string[] $needle
     *
     * @return static
     * @param bool $includeNeedle
     * @param int $offset
     */
    public function before($needle, $includeNeedle = false, $offset = 0)
    {
        $includeNeedle = (bool) $includeNeedle;
        $offset = (int) $offset;
        $str = clone $this;
        $i = \PHP_INT_MAX;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOf($n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
                $str->string = $n;
            }
        }

        if (\PHP_INT_MAX === $i) {
            return $str;
        }

        if ($includeNeedle) {
            $i += $str->length();
        }

        return $this->slice(0, $i);
    }

    /**
     * @param string|string[] $needle
     *
     * @return static
     * @param bool $includeNeedle
     * @param int $offset
     */
    public function beforeLast($needle, $includeNeedle = false, $offset = 0)
    {
        $includeNeedle = (bool) $includeNeedle;
        $offset = (int) $offset;
        $str = clone $this;
        $i = null;

        foreach ((array) $needle as $n) {
            $n = (string) $n;
            $j = $this->indexOfLast($n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
                $str->string = $n;
            }
        }

        if (null === $i) {
            return $str;
        }

        if ($includeNeedle) {
            $i += $str->length();
        }

        return $this->slice(0, $i);
    }

    /**
     * @return mixed[]
     * @param int $offset
     */
    public function bytesAt($offset)
    {
        $offset = (int) $offset;
        $str = $this->slice($offset, 1);

        return '' === $str->string ? [] : array_values(unpack('C*', $str->string));
    }

    /**
     * @return static
     */
    abstract public function camel();

    /**
     * @return mixed[]
     * @param int $length
     */
    abstract public function chunk($length = 1);

    /**
     * @return static
     */
    public function collapseWhitespace()
    {
        $str = clone $this;
        $str->string = trim(preg_replace('/(?:\s{2,}+|[^\S ])/', ' ', $str->string));

        return $str;
    }

    /**
     * @param string|string[] $needle
     * @return bool
     */
    public function containsAny($needle)
    {
        return null !== $this->indexOf($needle);
    }

    /**
     * @param string|string[] $suffix
     * @return bool
     */
    public function endsWith($suffix)
    {
        if (!\is_array($suffix) && !$suffix instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, static::class));
        }

        foreach ($suffix as $s) {
            if ($this->endsWith((string) $s)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     * @param string $suffix
     */
    public function ensureEnd($suffix)
    {
        $suffix = (string) $suffix;
        if (!$this->endsWith($suffix)) {
            return $this->append($suffix);
        }

        $suffix = preg_quote($suffix);
        $regex = '{('.$suffix.')(?:'.$suffix.')++$}D';

        return $this->replaceMatches($regex.($this->ignoreCase ? 'i' : ''), '$1');
    }

    /**
     * @return static
     * @param string $prefix
     */
    public function ensureStart($prefix)
    {
        $prefix = (string) $prefix;
        $prefix = new static($prefix);

        if (!$this->startsWith($prefix)) {
            return $this->prepend($prefix);
        }

        $str = clone $this;
        $i = $prefixLen = $prefix->length();

        while ($this->indexOf($prefix, $i) === $i) {
            $str = $str->slice($prefixLen);
            $i += $prefixLen;
        }

        return $str;
    }

    /**
     * @param string|string[] $string
     * @return bool
     */
    public function equalsTo($string)
    {
        if (!\is_array($string) && !$string instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, static::class));
        }

        foreach ($string as $s) {
            if ($this->equalsTo((string) $s)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     */
    abstract public function folded();

    /**
     * @return static
     */
    public function ignoreCase()
    {
        $str = clone $this;
        $str->ignoreCase = true;

        return $str;
    }

    /**
     * @param string|string[] $needle
     * @return int|null
     * @param int $offset
     */
    public function indexOf($needle, $offset = 0)
    {
        $offset = (int) $offset;
        if (!\is_array($needle) && !$needle instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, static::class));
        }

        $i = \PHP_INT_MAX;

        foreach ($needle as $n) {
            $j = $this->indexOf((string) $n, $offset);

            if (null !== $j && $j < $i) {
                $i = $j;
            }
        }

        return \PHP_INT_MAX === $i ? null : $i;
    }

    /**
     * @param string|string[] $needle
     * @return int|null
     * @param int $offset
     */
    public function indexOfLast($needle, $offset = 0)
    {
        $offset = (int) $offset;
        if (!\is_array($needle) && !$needle instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, static::class));
        }

        $i = null;

        foreach ($needle as $n) {
            $j = $this->indexOfLast((string) $n, $offset);

            if (null !== $j && $j >= $i) {
                $i = $offset = $j;
            }
        }

        return $i;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return '' === $this->string;
    }

    /**
     * @return static
     * @param string $lastGlue
     */
    abstract public function join(array $strings, $lastGlue = null);

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->string;
    }

    /**
     * @return int
     */
    abstract public function length();

    /**
     * @return static
     */
    abstract public function lower();

    /**
     * Matches the string using a regular expression.
     *
     * Pass PREG_PATTERN_ORDER or PREG_SET_ORDER as $flags to get all occurrences matching the regular expression.
     *
     * @return array All matches in a multi-dimensional array ordered according to flags
     * @param string $regexp
     * @param int $flags
     * @param int $offset
     */
    abstract public function match($regexp, $flags = 0, $offset = 0);

    /**
     * @return static
     * @param int $length
     * @param string $padStr
     */
    abstract public function padBoth($length, $padStr = ' ');

    /**
     * @return static
     * @param int $length
     * @param string $padStr
     */
    abstract public function padEnd($length, $padStr = ' ');

    /**
     * @return static
     * @param int $length
     * @param string $padStr
     */
    abstract public function padStart($length, $padStr = ' ');

    /**
     * @return static
     * @param string ...$prefix
     */
    abstract public function prepend(...$prefix);

    /**
     * @return static
     * @param int $multiplier
     */
    public function repeat($multiplier)
    {
        $multiplier = (int) $multiplier;
        if (0 > $multiplier) {
            throw new InvalidArgumentException(sprintf('Multiplier must be positive, %d given.', $multiplier));
        }

        $str = clone $this;
        $str->string = str_repeat($str->string, $multiplier);

        return $str;
    }

    /**
     * @return static
     * @param string $from
     * @param string $to
     */
    abstract public function replace($from, $to);

    /**
     * @param string|callable $to
     *
     * @return static
     * @param string $fromRegexp
     */
    abstract public function replaceMatches($fromRegexp, $to);

    /**
     * @return static
     */
    abstract public function reverse();

    /**
     * @return static
     * @param int $start
     * @param int $length
     */
    abstract public function slice($start = 0, $length = null);

    /**
     * @return static
     */
    abstract public function snake();

    /**
     * @return static
     * @param string $replacement
     * @param int $start
     * @param int $length
     */
    abstract public function splice($replacement, $start = 0, $length = null);

    /**
     * @return mixed[]
     * @param string $delimiter
     * @param int $limit
     * @param int $flags
     */
    public function split($delimiter, $limit = null, $flags = null)
    {
        $delimiter = (string) $delimiter;
        if (null === $flags) {
            throw new \TypeError('Split behavior when $flags is null must be implemented by child classes.');
        }

        if ($this->ignoreCase) {
            $delimiter .= 'i';
        }

        set_error_handler(static function ($t, $m) { throw new InvalidArgumentException($m); });

        try {
            if (false === $chunks = preg_split($delimiter, $this->string, $limit, $flags)) {
                $lastError = preg_last_error();

                foreach (get_defined_constants(true)['pcre'] as $k => $v) {
                    if ($lastError === $v && '_ERROR' === substr($k, -6)) {
                        throw new RuntimeException('Splitting failed with '.$k.'.');
                    }
                }

                throw new RuntimeException('Splitting failed with unknown error code.');
            }
        } finally {
            restore_error_handler();
        }

        $str = clone $this;

        if (self::PREG_SPLIT_OFFSET_CAPTURE & $flags) {
            foreach ($chunks as &$chunk) {
                $str->string = $chunk[0];
                $chunk[0] = clone $str;
            }
        } else {
            foreach ($chunks as &$chunk) {
                $str->string = $chunk;
                $chunk = clone $str;
            }
        }

        return $chunks;
    }

    /**
     * @param string|string[] $prefix
     * @return bool
     */
    public function startsWith($prefix)
    {
        if (!\is_array($prefix) && !$prefix instanceof \Traversable) {
            throw new \TypeError(sprintf('Method "%s()" must be overridden by class "%s" to deal with non-iterable values.', __FUNCTION__, static::class));
        }

        foreach ($prefix as $prefix) {
            if ($this->startsWith((string) $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return static
     * @param bool $allWords
     */
    abstract public function title($allWords = false);

    /**
     * @param string $toEncoding
     * @return \Symfony\Component\String\ByteString
     */
    public function toByteString($toEncoding = null)
    {
        $b = new ByteString();

        $toEncoding = \in_array($toEncoding, ['utf8', 'utf-8', 'UTF8'], true) ? 'UTF-8' : $toEncoding;

        if (null === $toEncoding || $toEncoding === $fromEncoding = $this instanceof AbstractUnicodeString || preg_match('//u', $b->string) ? 'UTF-8' : 'Windows-1252') {
            $b->string = $this->string;

            return $b;
        }

        set_error_handler(static function ($t, $m) { throw new InvalidArgumentException($m); });

        try {
            try {
                $b->string = mb_convert_encoding($this->string, $toEncoding, 'UTF-8');
            } catch (InvalidArgumentException $e) {
                if (!\function_exists('iconv')) {
                    throw $e;
                }

                $b->string = iconv('UTF-8', $toEncoding, $this->string);
            }
        } finally {
            restore_error_handler();
        }

        return $b;
    }

    /**
     * @return \Symfony\Component\String\CodePointString
     */
    public function toCodePointString()
    {
        return new CodePointString($this->string);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->string;
    }

    /**
     * @return \Symfony\Component\String\UnicodeString
     */
    public function toUnicodeString()
    {
        return new UnicodeString($this->string);
    }

    /**
     * @return static
     * @param string $chars
     */
    abstract public function trim($chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}");

    /**
     * @return static
     * @param string $chars
     */
    abstract public function trimEnd($chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}");

    /**
     * @return static
     * @param string $chars
     */
    abstract public function trimStart($chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}");

    /**
     * @return static
     * @param int $length
     * @param string $ellipsis
     * @param bool $cut
     */
    public function truncate($length, $ellipsis = '', $cut = true)
    {
        $length = (int) $length;
        $ellipsis = (string) $ellipsis;
        $cut = (bool) $cut;
        $stringLength = $this->length();

        if ($stringLength <= $length) {
            return clone $this;
        }

        $ellipsisLength = '' !== $ellipsis ? (new static($ellipsis))->length() : 0;

        if ($length < $ellipsisLength) {
            $ellipsisLength = 0;
        }

        if (!$cut) {
            if (null === $length = $this->indexOf([' ', "\r", "\n", "\t"], ($length ?: 1) - 1)) {
                return clone $this;
            }

            $length += $ellipsisLength;
        }

        $str = $this->slice(0, $length - $ellipsisLength);

        return $ellipsisLength ? $str->trimEnd()->append($ellipsis) : $str;
    }

    /**
     * @return static
     */
    abstract public function upper();

    /**
     * Returns the printable length on a terminal.
     * @param bool $ignoreAnsiDecoration
     * @return int
     */
    abstract public function width($ignoreAnsiDecoration = true);

    /**
     * @return static
     * @param int $width
     * @param string $break
     * @param bool $cut
     */
    public function wordwrap($width = 75, $break = "\n", $cut = false)
    {
        $width = (int) $width;
        $break = (string) $break;
        $cut = (bool) $cut;
        $lines = '' !== $break ? $this->split($break) : [clone $this];
        $chars = [];
        $mask = '';

        if (1 === \count($lines) && '' === $lines[0]->string) {
            return $lines[0];
        }

        foreach ($lines as $i => $line) {
            if ($i) {
                $chars[] = $break;
                $mask .= '#';
            }

            foreach ($line->chunk() as $char) {
                $chars[] = $char->string;
                $mask .= ' ' === $char->string ? ' ' : '?';
            }
        }

        $string = '';
        $j = 0;
        $b = $i = -1;
        $mask = wordwrap($mask, $width, '#', $cut);

        while (false !== $b = strpos($mask, '#', $b + 1)) {
            for (++$i; $i < $b; ++$i) {
                $string .= $chars[$j];
                unset($chars[$j++]);
            }

            if ($break === $chars[$j] || ' ' === $chars[$j]) {
                unset($chars[$j++]);
            }

            $string .= $break;
        }

        $str = clone $this;
        $str->string = $string.implode('', $chars);

        return $str;
    }

    /**
     * @return mixed[]
     */
    public function __sleep()
    {
        return ['string'];
    }

    public function __clone()
    {
        $this->ignoreCase = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }
}
