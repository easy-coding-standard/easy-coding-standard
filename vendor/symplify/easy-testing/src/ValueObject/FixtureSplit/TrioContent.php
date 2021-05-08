<?php

namespace Symplify\EasyTesting\ValueObject\FixtureSplit;

final class TrioContent
{
    /**
     * @var string
     */
    private $firstValue;
    /**
     * @var string
     */
    private $secondValue;
    /**
     * @var string
     */
    private $expectedResult;
    /**
     * @param string $firstValue
     */
    public function __construct($firstValue, string $secondValue, string $expectedResult)
    {
        if (\is_object($firstValue)) {
            $firstValue = (string) $firstValue;
        }
        $this->firstValue = $firstValue;
        $this->secondValue = $secondValue;
        $this->expectedResult = $expectedResult;
    }
    /**
     * @return string
     */
    public function getFirstValue()
    {
        return $this->firstValue;
    }
    /**
     * @return string
     */
    public function getSecondValue()
    {
        return $this->secondValue;
    }
    /**
     * @return string
     */
    public function getExpectedResult()
    {
        return $this->expectedResult;
    }
}
