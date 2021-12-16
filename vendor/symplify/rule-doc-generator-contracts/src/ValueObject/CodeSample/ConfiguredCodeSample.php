<?php

declare (strict_types=1);
namespace ECSPrefix20211216\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20211216\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20211216\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use ECSPrefix20211216\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ConfiguredCodeSample extends \ECSPrefix20211216\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample implements \ECSPrefix20211216\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
{
    /**
     * @var mixed[]
     */
    private $configuration = [];
    /**
     * @param mixed[] $configuration
     */
    public function __construct(string $badCode, string $goodCode, array $configuration)
    {
        if ($configuration === []) {
            $message = \sprintf('Configuration cannot be empty. Look for "%s"', $badCode);
            throw new \ECSPrefix20211216\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($message);
        }
        $this->configuration = $configuration;
        parent::__construct($badCode, $goodCode);
    }
    /**
     * @return mixed[]
     */
    public function getConfiguration() : array
    {
        return $this->configuration;
    }
}
