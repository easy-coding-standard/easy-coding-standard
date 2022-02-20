<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject;

use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
abstract class AbstractCodeSample implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
{
    /**
     * @var non-empty-string
     */
    private $goodCode;
    /**
     * @var non-empty-string
     */
    private $badCode;
    public function __construct(string $badCode, string $goodCode)
    {
        $badCode = \trim($badCode);
        $goodCode = \trim($goodCode);
        if ($badCode === '') {
            throw new \ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException('Bad sample good code cannot be empty');
        }
        if ($goodCode === '') {
            throw new \ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException('Good sample good code cannot be empty');
        }
        if ($goodCode === $badCode) {
            $errorMessage = \sprintf('Good and bad code cannot be identical: "%s"', $goodCode);
            throw new \ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($errorMessage);
        }
        $this->goodCode = $goodCode;
        $this->badCode = $badCode;
    }
    public function getGoodCode() : string
    {
        return $this->goodCode;
    }
    public function getBadCode() : string
    {
        return $this->badCode;
    }
}
