<?php

namespace ECSPrefix20210514\Symplify\EasyTesting\ValueObject\FixtureSplit;

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
     * @param string $secondValue
     * @param string $expectedResult
     */
    public function __construct($firstValue, $secondValue, $expectedResult)
    {
        $firstValue = (string) $firstValue;
        $secondValue = (string) $secondValue;
        $expectedResult = (string) $expectedResult;
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
