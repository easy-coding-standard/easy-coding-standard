<?php

namespace Symplify\EasyCodingStandard\FixerRunner;

use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Exception\Configuration\WhitespaceConfigurationException;
use Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
final class WhitespacesFixerConfigFactory
{
    /**
     * @var ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @return \PhpCsFixer\WhitespacesFixerConfig
     */
    public function create()
    {
        $lineEnding = $this->parameterProvider->provideParameter('line_ending');
        if ($lineEnding === '\\n') {
            $lineEnding = "\n";
        }
        return new \PhpCsFixer\WhitespacesFixerConfig($this->resolveIndentation(), $lineEnding);
    }
    /**
     * @return string
     */
    private function resolveIndentation()
    {
        $indentation = $this->parameterProvider->provideParameter('indentation');
        if ($this->isOneTab($indentation)) {
            return \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::ONE_TAB;
        }
        if ($indentation === \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::TWO_SPACES) {
            return \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::TWO_SPACES;
        }
        if ($this->isFourSpaces($indentation)) {
            return \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::FOUR_SPACES;
        }
        $allowedValues = ['tab', 'spaces', \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::TWO_SPACES, \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::FOUR_SPACES, \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::ONE_TAB];
        throw new \Symplify\EasyCodingStandard\Exception\Configuration\WhitespaceConfigurationException(\sprintf('Value "%s" is not supported in "parameters > indentation".%sUse one of: "%s".', $indentation, \PHP_EOL, \implode('", "', $allowedValues)));
    }
    /**
     * @param string $indentation
     */
    private function isOneTab($indentation) : bool
    {
        if (\is_object($indentation)) {
            $indentation = (string) $indentation;
        }
        if ($indentation === 'tab') {
            return \true;
        }
        return $indentation === \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::ONE_TAB;
    }
    /**
     * @param string $indentation
     */
    private function isFourSpaces($indentation) : bool
    {
        if (\is_object($indentation)) {
            $indentation = (string) $indentation;
        }
        if ($indentation === 'spaces') {
            return \true;
        }
        return $indentation === \Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing::FOUR_SPACES;
    }
}
