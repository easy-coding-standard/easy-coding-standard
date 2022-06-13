<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner;

use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Exception\Configuration\WhitespaceConfigurationException;
use Symplify\EasyCodingStandard\FixerRunner\ValueObject\Spacing;
use ECSPrefix202206\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class WhitespacesFixerConfigFactory
{
    /**
     * @var string[]
     */
    private const ALLOWED_VALUES = ['tab', 'spaces', Spacing::TWO_SPACES, Spacing::FOUR_SPACES, Spacing::ONE_TAB];
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    public function create() : WhitespacesFixerConfig
    {
        $lineEnding = $this->parameterProvider->provideParameter('line_ending');
        if ($lineEnding === '\\n') {
            $lineEnding = "\n";
        }
        return new WhitespacesFixerConfig($this->resolveIndentation(), $lineEnding);
    }
    private function resolveIndentation() : string
    {
        $indentation = $this->parameterProvider->provideStringParameter('indentation');
        if ($this->isOneTab($indentation)) {
            return Spacing::ONE_TAB;
        }
        if ($indentation === Spacing::TWO_SPACES) {
            return Spacing::TWO_SPACES;
        }
        if ($this->isFourSpaces($indentation)) {
            return Spacing::FOUR_SPACES;
        }
        throw new WhitespaceConfigurationException(\sprintf('Value "%s" is not supported in "$ecsConfig->indentation(...)".%sUse one of: "%s".', $indentation, \PHP_EOL, \implode('", "', self::ALLOWED_VALUES)));
    }
    private function isOneTab(string $indentation) : bool
    {
        if ($indentation === 'tab') {
            return \true;
        }
        return $indentation === Spacing::ONE_TAB;
    }
    private function isFourSpaces(string $indentation) : bool
    {
        if ($indentation === 'spaces') {
            return \true;
        }
        return $indentation === Spacing::FOUR_SPACES;
    }
}
