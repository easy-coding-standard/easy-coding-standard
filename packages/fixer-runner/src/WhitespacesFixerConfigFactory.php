<?php

declare(strict_types=1);

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

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    public function create(): WhitespacesFixerConfig
    {
        $lineEnding = $this->parameterProvider->provideParameter('line_ending');

        if ($lineEnding === '\n') {
            $lineEnding = "\n";
        }

        return new WhitespacesFixerConfig($this->resolveIndentation(), $lineEnding);
    }

    private function resolveIndentation(): string
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

        throw new WhitespaceConfigurationException(sprintf(
            'Value "%s" is not supported in "parameters > indentation".%sUse one of: "%s".',
            $indentation,
            PHP_EOL,
            implode('", "', $allowedValues)
        ));
    }

    private function isOneTab(string $indentation): bool
    {
        if ($indentation === 'tab') {
            return true;
        }

        return $indentation === Spacing::ONE_TAB;
    }

    private function isFourSpaces(string $indentation): bool
    {
        if ($indentation === 'spaces') {
            return true;
        }

        return $indentation === Spacing::FOUR_SPACES;
    }
}
