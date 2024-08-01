<?php

declare (strict_types=1);
namespace ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject;

use ECSPrefix202408\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix202408\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
abstract class AbstractCodeSample implements CodeSampleInterface
{
    /**
     * @var non-empty-string
     * @readonly
     */
    private $goodCode;
    /**
     * @var non-empty-string
     * @readonly
     */
    private $badCode;
    public function __construct(string $badCode, string $goodCode)
    {
        $badCode = \trim($badCode);
        $goodCode = \trim($goodCode);
        if ($badCode === '') {
            throw new ShouldNotHappenException('Bad sample good code cannot be empty');
        }
        if ($goodCode === '') {
            throw new ShouldNotHappenException('Good sample good code cannot be empty');
        }
        if ($goodCode === $badCode) {
            $errorMessage = \sprintf('Good and bad code cannot be identical: "%s"', $goodCode);
            throw new ShouldNotHappenException($errorMessage);
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
