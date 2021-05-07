<?php

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

final class LineLengthAndPosition
{
    /**
     * @var int
     */
    private $lineLength;
    /**
     * @var int
     */
    private $currentPosition;
    /**
     * @param int $lineLength
     * @param int $currentPosition
     */
    public function __construct($lineLength, $currentPosition)
    {
        $this->lineLength = $lineLength;
        $this->currentPosition = $currentPosition;
    }
    /**
     * @return int
     */
    public function getLineLength()
    {
        return $this->lineLength;
    }
    /**
     * @return int
     */
    public function getCurrentPosition()
    {
        return $this->currentPosition;
    }
}
