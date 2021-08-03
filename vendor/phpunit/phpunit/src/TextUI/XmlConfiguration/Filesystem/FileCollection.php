<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration;

use function count;
use Countable;
use IteratorAggregate;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class FileCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var File[]
     */
    private $files;
    /**
     * @param File[] $files
     */
    public static function fromArray(array $files) : self
    {
        return new self(...$files);
    }
    private function __construct(\ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\File ...$files)
    {
        $this->files = $files;
    }
    /**
     * @return File[]
     */
    public function asArray() : array
    {
        return $this->files;
    }
    public function count() : int
    {
        return \count($this->files);
    }
    public function getIterator() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\FileCollectionIterator
    {
        return new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\FileCollectionIterator($this);
    }
    public function isEmpty() : bool
    {
        return $this->count() === 0;
    }
}
