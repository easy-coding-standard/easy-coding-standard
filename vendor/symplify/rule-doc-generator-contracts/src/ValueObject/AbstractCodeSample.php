<?php

namespace ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject;

use ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20210516\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
abstract class AbstractCodeSample implements \ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
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
     * @param string $goodCode
     */
    public function __construct($badCode, $goodCode)
    {
        $badCode = (string) $badCode;
        $goodCode = (string) $goodCode;
        $badCode = \trim($badCode);
        $goodCode = \trim($goodCode);
        if ($badCode === '') {
            throw new \ECSPrefix20210516\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException('Bad sample good code cannot be empty');
        }
        if ($goodCode === $badCode) {
            $errorMessage = \sprintf('Good and bad code cannot be identical: "%s"', $goodCode);
            throw new \ECSPrefix20210516\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($errorMessage);
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
