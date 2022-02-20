<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ConfiguredCodeSample extends \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
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
            throw new \ECSPrefix20220220\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($message);
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
