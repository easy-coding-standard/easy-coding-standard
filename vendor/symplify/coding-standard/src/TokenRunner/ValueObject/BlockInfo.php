<?php

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

final class BlockInfo
{
    /**
     * @var int
     */
    private $start;
    /**
     * @var int
     */
    private $end;
    /**
     * @param int $start
     * @param int $end
     */
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
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
    public function getEnd()
    {
        return $this->end;
    }
    /**
     * @param int $position
     * @return bool
     */
    public function contains($position)
    {
        if ($position < $this->start) {
            return \false;
        }
        return $position <= $this->end;
    }
}
