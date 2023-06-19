<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix202306\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix202306\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use ECSPrefix202306\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ConfiguredCodeSample extends AbstractCodeSample implements CodeSampleInterface
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
            throw new ShouldNotHappenException($message);
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
