<?php

namespace ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use ECSPrefix20210516\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ConfiguredCodeSample extends \ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample implements \ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\CodeSampleInterface
{
    /**
     * @var array<string, mixed>
     */
    private $configuration = [];
    /**
     * @param array<string, mixed> $configuration
     * @param string $badCode
     * @param string $goodCode
     */
    public function __construct($badCode, $goodCode, array $configuration)
    {
        $badCode = (string) $badCode;
        $goodCode = (string) $goodCode;
        if ($configuration === []) {
            $message = \sprintf('Configuration cannot be empty. Look for "%s"', $badCode);
            throw new \ECSPrefix20210516\Symplify\RuleDocGenerator\Exception\ShouldNotHappenException($message);
        }
        $this->configuration = $configuration;
        parent::__construct($badCode, $goodCode);
    }
    /**
     * @return mixed[]
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
