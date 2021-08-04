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
namespace ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration;

use function count;
use function iterator_count;
use Countable;
use Iterator;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestSuiteCollectionIterator implements \Countable, \Iterator
{
    /**
     * @var TestSuite[]
     */
    private $testSuites;
    /**
     * @var int
     */
    private $position;
    public function __construct(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestSuiteCollection $testSuites)
    {
        $this->testSuites = $testSuites->asArray();
    }
    public function count() : int
    {
        return \iterator_count($this);
    }
    public function rewind() : void
    {
        $this->position = 0;
    }
    public function valid() : bool
    {
        return $this->position < \count($this->testSuites);
    }
    public function key() : int
    {
        return $this->position;
    }
    public function current() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestSuite
    {
        return $this->testSuites[$this->position];
    }
    public function next() : void
    {
        $this->position++;
    }
}
