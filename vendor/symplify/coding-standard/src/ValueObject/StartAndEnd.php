<?php

namespace Symplify\CodingStandard\ValueObject;

final class StartAndEnd
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
}
