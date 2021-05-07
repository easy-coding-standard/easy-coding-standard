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
    /**
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     */
    public function __construct($parameterProvider)
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
        return new WhitespacesFixerConfig($this->resolveIndentation(), $lineEnding);
    }
    /**
     * @return string
     */
    private function resolveIndentation()
    {
        $indentation = $this->parameterProvider->provideParameter('indentation');
        if ($this->isOneTab($indentation)) {
            return Spacing::ONE_TAB;
        }
        if ($indentation === Spacing::TWO_SPACES) {
            return Spacing::TWO_SPACES;
        }
        if ($this->isFourSpaces($indentation)) {
            return Spacing::FOUR_SPACES;
        }
        $allowedValues = ['tab', 'spaces', Spacing::TWO_SPACES, Spacing::FOUR_SPACES, Spacing::ONE_TAB];
        throw new WhitespaceConfigurationException(\sprintf('Value "%s" is not supported in "parameters > indentation".%sUse one of: "%s".', $indentation, \PHP_EOL, \implode('", "', $allowedValues)));
    }
    /**
     * @param string $indentation
     * @return bool
     */
    private function isOneTab($indentation)
    {
        if ($indentation === 'tab') {
            return \true;
        }
        return $indentation === Spacing::ONE_TAB;
    }
    /**
     * @param string $indentation
     * @return bool
     */
    private function isFourSpaces($indentation)
    {
        if ($indentation === 'spaces') {
            return \true;
        }
        return $indentation === Spacing::FOUR_SPACES;
    }
}
