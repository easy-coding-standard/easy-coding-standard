<?php

namespace ECSPrefix20210515\Nette\Utils;

use ECSPrefix20210515\Nette;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
/**
 * Finder allows searching through directory trees using iterator.
 *
 * <code>
 * Finder::findFiles('*.php')
 *     ->size('> 10kB')
 *     ->from('.')
 *     ->exclude('temp');
 * </code>
 */
class Finder implements \IteratorAggregate, \Countable
{
    use Nette\SmartObject;
    /** @var callable  extension methods */
    private static $extMethods = [];
    /** @var array */
    private $paths = [];
    /** @var array of filters */
    private $groups = [];
    /** @var array filter for recursive traversing */
    private $exclude = [];
    /** @var int */
    private $order = \RecursiveIteratorIterator::SELF_FIRST;
    /** @var int */
    private $maxDepth = -1;
    /** @var array */
    private $cursor;
    /**
     * Begins search for files matching mask and all directories.
     * @param  string|string[]  $masks
     * @return static
     */
    public static function find(...$masks)
    {
        $masks = $masks && \is_array($masks[0]) ? $masks[0] : $masks;
        return (new static())->select($masks, 'isDir')->select($masks, 'isFile');
    }
    /**
     * Begins search for files matching mask.
     * @param  string|string[]  $masks
     * @return static
     */
    public static function findFiles(...$masks)
    {
        $masks = $masks && \is_array($masks[0]) ? $masks[0] : $masks;
        return (new static())->select($masks, 'isFile');
    }
    /**
     * Begins search for directories matching mask.
     * @param  string|string[]  $masks
     * @return static
     */
    public static function findDirectories(...$masks)
    {
        $masks = $masks && \is_array($masks[0]) ? $masks[0] : $masks;
        return (new static())->select($masks, 'isDir');
    }
    /**
     * Creates filtering group by mask & type selector.
     * @return static
     * @param string $type
     */
    private function select(array $masks, $type)
    {
        $type = (string) $type;
        $this->cursor =& $this->groups[];
        $pattern = self::buildPattern($masks);
        $this->filter(function (\RecursiveDirectoryIterator $file) use($type, $pattern) : bool {
            return !$file->isDot() && $file->{$type}() && (!$pattern || \preg_match($pattern, '/' . \strtr($file->getSubPathName(), '\\', '/')));
        });
        return $this;
    }
    /**
     * Searches in the given folder(s).
     * @param  string|string[]  $paths
     * @return static
     */
    public function in(...$paths)
    {
        $this->maxDepth = 0;
        return $this->from(...$paths);
    }
    /**
     * Searches recursively from the given folder(s).
     * @param  string|string[]  $paths
     * @return static
     */
    public function from(...$paths)
    {
        if ($this->paths) {
            throw new \ECSPrefix20210515\Nette\InvalidStateException('Directory to search has already been specified.');
        }
        $this->paths = \is_array($paths[0]) ? $paths[0] : $paths;
        $this->cursor =& $this->exclude;
        return $this;
    }
    /**
     * Shows folder content prior to the folder.
     * @return static
     */
    public function childFirst()
    {
        $this->order = \RecursiveIteratorIterator::CHILD_FIRST;
        return $this;
    }
    /**
     * Converts Finder pattern to regular expression.
     * @return string|null
     */
    private static function buildPattern(array $masks)
    {
        $pattern = [];
        foreach ($masks as $mask) {
            $mask = \rtrim(\strtr($mask, '\\', '/'), '/');
            $prefix = '';
            if ($mask === '') {
                continue;
            } elseif ($mask === '*') {
                return null;
            } elseif ($mask[0] === '/') {
                // absolute fixing
                $mask = \ltrim($mask, '/');
                $prefix = '(?<=^/)';
            }
            $pattern[] = $prefix . \strtr(\preg_quote($mask, '#'), ['\\*\\*' => '.*', '\\*' => '[^/]*', '\\?' => '[^/]', '\\[\\!' => '[^', '\\[' => '[', '\\]' => ']', '\\-' => '-']);
        }
        return $pattern ? '#/(' . \implode('|', $pattern) . ')$#Di' : null;
    }
    /********************* iterator generator ****************d*g**/
    /**
     * Get the number of found files and/or directories.
     * @return int
     */
    public function count()
    {
        return \iterator_count($this->getIterator());
    }
    /**
     * Returns iterator.
     * @return \Iterator
     */
    public function getIterator()
    {
        if (!$this->paths) {
            throw new \ECSPrefix20210515\Nette\InvalidStateException('Call in() or from() to specify directory to search.');
        } elseif (\count($this->paths) === 1) {
            return $this->buildIterator((string) $this->paths[0]);
        } else {
            $iterator = new \AppendIterator();
            foreach ($this->paths as $path) {
                $iterator->append($this->buildIterator((string) $path));
            }
            return $iterator;
        }
    }
    /**
     * Returns per-path iterator.
     * @param string $path
     * @return \Iterator
     */
    private function buildIterator($path)
    {
        $path = (string) $path;
        $iterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
        if ($this->exclude) {
            $iterator = new \RecursiveCallbackFilterIterator($iterator, function ($foo, $bar, \RecursiveDirectoryIterator $file) : bool {
                if (!$file->isDot() && !$file->isFile()) {
                    foreach ($this->exclude as $filter) {
                        if (!$filter($file)) {
                            return \false;
                        }
                    }
                }
                return \true;
            });
        }
        if ($this->maxDepth !== 0) {
            $iterator = new \RecursiveIteratorIterator($iterator, $this->order);
            $iterator->setMaxDepth($this->maxDepth);
        }
        $iterator = new \CallbackFilterIterator($iterator, function ($foo, $bar, \Iterator $file) : bool {
            while ($file instanceof \OuterIterator) {
                $file = $file->getInnerIterator();
            }
            foreach ($this->groups as $filters) {
                foreach ($filters as $filter) {
                    if (!$filter($file)) {
                        continue 2;
                    }
                }
                return \true;
            }
            return \false;
        });
        return $iterator;
    }
    /********************* filtering ****************d*g**/
    /**
     * Restricts the search using mask.
     * Excludes directories from recursive traversing.
     * @param  string|string[]  $masks
     * @return static
     */
    public function exclude(...$masks)
    {
        $masks = $masks && \is_array($masks[0]) ? $masks[0] : $masks;
        $pattern = self::buildPattern($masks);
        if ($pattern) {
            $this->filter(function (\RecursiveDirectoryIterator $file) use($pattern) : bool {
                return !\preg_match($pattern, '/' . \strtr($file->getSubPathName(), '\\', '/'));
            });
        }
        return $this;
    }
    /**
     * Restricts the search using callback.
     * @param  callable  $callback  function (RecursiveDirectoryIterator $file): bool
     * @return static
     */
    public function filter(callable $callback)
    {
        $this->cursor[] = $callback;
        return $this;
    }
    /**
     * Limits recursion level.
     * @return static
     * @param int $depth
     */
    public function limitDepth($depth)
    {
        $depth = (int) $depth;
        $this->maxDepth = $depth;
        return $this;
    }
    /**
     * Restricts the search by size.
     * @param  string  $operator  "[operator] [size] [unit]" example: >=10kB
     * @return static
     * @param int $size
     */
    public function size($operator, $size = null)
    {
        $operator = (string) $operator;
        if (\func_num_args() === 1) {
            // in $operator is predicate
            if (!\preg_match('#^(?:([=<>!]=?|<>)\\s*)?((?:\\d*\\.)?\\d+)\\s*(K|M|G|)B?$#Di', $operator, $matches)) {
                throw new \ECSPrefix20210515\Nette\InvalidArgumentException('Invalid size predicate format.');
            }
            list(, $operator, $size, $unit) = $matches;
            static $units = ['' => 1, 'k' => 1000.0, 'm' => 1000000.0, 'g' => 1000000000.0];
            $size *= $units[\strtolower($unit)];
            $operator = $operator ?: '=';
        }
        return $this->filter(function (\RecursiveDirectoryIterator $file) use($operator, $size) : bool {
            return self::compare($file->getSize(), $operator, $size);
        });
    }
    /**
     * Restricts the search by modified time.
     * @param  string  $operator  "[operator] [date]" example: >1978-01-23
     * @param  string|int|\DateTimeInterface  $date
     * @return static
     */
    public function date($operator, $date = null)
    {
        $operator = (string) $operator;
        if (\func_num_args() === 1) {
            // in $operator is predicate
            if (!\preg_match('#^(?:([=<>!]=?|<>)\\s*)?(.+)$#Di', $operator, $matches)) {
                throw new \ECSPrefix20210515\Nette\InvalidArgumentException('Invalid date predicate format.');
            }
            list(, $operator, $date) = $matches;
            $operator = $operator ?: '=';
        }
        $date = \ECSPrefix20210515\Nette\Utils\DateTime::from($date)->format('U');
        return $this->filter(function (\RecursiveDirectoryIterator $file) use($operator, $date) : bool {
            return self::compare($file->getMTime(), $operator, $date);
        });
    }
    /**
     * Compares two values.
     * @param string $operator
     * @return bool
     */
    public static function compare($l, $operator, $r)
    {
        $operator = (string) $operator;
        switch ($operator) {
            case '>':
                return $l > $r;
            case '>=':
                return $l >= $r;
            case '<':
                return $l < $r;
            case '<=':
                return $l <= $r;
            case '=':
            case '==':
                return $l == $r;
            case '!':
            case '!=':
            case '<>':
                return $l != $r;
            default:
                throw new \ECSPrefix20210515\Nette\InvalidArgumentException("Unknown operator {$operator}.");
        }
    }
    /********************* extension methods ****************d*g**/
    /**
     * @param string $name
     */
    public function __call($name, array $args)
    {
        $name = (string) $name;
        return isset(self::$extMethods[$name]) ? self::$extMethods[$name]($this, ...$args) : \ECSPrefix20210515\Nette\Utils\ObjectHelpers::strictCall(\get_class($this), $name, \array_keys(self::$extMethods));
    }
    /**
     * @return void
     * @param string $name
     */
    public static function extensionMethod($name, callable $callback)
    {
        $name = (string) $name;
        self::$extMethods[$name] = $callback;
    }
}
