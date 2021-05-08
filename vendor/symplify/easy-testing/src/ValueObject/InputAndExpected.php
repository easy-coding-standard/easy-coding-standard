<?php

namespace Symplify\EasyTesting\ValueObject;

final class InputAndExpected
{
    /**
     * @var string
     */
    private $input;
    /**
     * @var mixed
     */
    private $expected;
    /**
     * @param mixed $expected
     * @param string $original
     */
    public function __construct($original, $expected)
    {
        if (\is_object($original)) {
            $original = (string) $original;
        }
        $this->input = $original;
        $this->expected = $expected;
    }
    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }
    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
