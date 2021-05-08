<?php

namespace Symplify\RuleDocGenerator\ValueObject;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
abstract class AbstractCodeSample implements \Symplify\RuleDocGenerator\Contract\CodeSampleInterface
{
    /**
     * @var string
     */
    private $goodCode;
    /**
     * @var string
     */
    private $badCode;
    /**
     * @param string $badCode
     */
    public function __construct($badCode, string $goodCode)
    {
        if (\is_object($badCode)) {
            $badCode = (string) $badCode;
        }
        $badCode = \trim($badCode);
        $goodCode = \trim($goodCode);
        if ($badCode === '') {
            throw new \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException('Bad sample good code cannot be empty');
        }
        if ($goodCode === $badCode) {
            $errorMessage = \sprintf('Good and bad code cannot be identical: "%s"', $goodCode);
            throw new \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($errorMessage);
        }
        $this->goodCode = $goodCode;
        $this->badCode = $badCode;
    }
    /**
     * @return string
     */
    public function getGoodCode()
    {
        return $this->goodCode;
    }
    /**
     * @return string
     */
    public function getBadCode()
    {
        return $this->badCode;
    }
}
