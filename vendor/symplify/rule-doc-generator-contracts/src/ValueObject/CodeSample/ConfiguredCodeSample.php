<?php

declare (strict_types=1);
namespace ECSPrefix20210825\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20210825\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20210825\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use ECSPrefix20210825\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ConfiguredCodeSample extends \ECSPrefix20210825\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample implements \ECSPrefix20210825\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
{
    /**
     * @var array<string, mixed>
     */
    private $configuration = [];
    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(string $badCode, string $goodCode, array $configuration)
    {
        if ($configuration === []) {
            $message = \sprintf('Configuration cannot be empty. Look for "%s"', $badCode);
            throw new \ECSPrefix20210825\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($message);
        }
        $this->configuration = $configuration;
        parent::__construct($badCode, $goodCode);
    }
    /**
     * @return array<string, mixed>
     */
    public function getConfiguration() : array
    {
        return $this->configuration;
    }
}
