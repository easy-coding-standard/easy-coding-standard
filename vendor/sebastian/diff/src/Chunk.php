<?php

/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\SebastianBergmann\Diff;

final class Chunk
{
    /**
     * @var int
     */
    private $start;
    /**
     * @var int
     */
    private $startRange;
    /**
     * @var int
     */
    private $end;
    /**
     * @var int
     */
    private $endRange;
    /**
     * @var Line[]
     */
    private $lines;
    /**
     * @param int $start
     * @param int $startRange
     * @param int $end
     * @param int $endRange
     */
    public function __construct($start = 0, $startRange = 1, $end = 0, $endRange = 1, array $lines = [])
    {
        $this->start = $start;
        $this->startRange = $startRange;
        $this->end = $end;
        $this->endRange = $endRange;
        $this->lines = $lines;
    }
    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }
    /**
     * @return int
     */
    public function getStartRange()
    {
        return $this->startRange;
    }
    /**
     * @return int
     */
    public function getEnd()
    {
        return $this->end;
    }
    /**
     * @return int
     */
    public function getEndRange()
    {
        return $this->endRange;
    }
    /**
     * @return mixed[]
     */
    public function getLines()
    {
        return $this->lines;
    }
    /**
     * @param Line[] $lines
     * @return void
     */
    public function setLines(array $lines)
    {
        foreach ($lines as $line) {
            if (!$line instanceof \ECSPrefix20210507\SebastianBergmann\Diff\Line) {
                throw new \ECSPrefix20210507\SebastianBergmann\Diff\InvalidArgumentException();
            }
        }
        $this->lines = $lines;
    }
}
