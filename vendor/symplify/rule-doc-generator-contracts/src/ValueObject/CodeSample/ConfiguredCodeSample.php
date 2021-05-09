<?php

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ConfiguredCodeSample extends AbstractCodeSample implements CodeSampleInterface
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
            $message = sprintf('Configuration cannot be empty. Look for "%s"', $badCode);
            throw new ShouldNotHappenException($message);
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
