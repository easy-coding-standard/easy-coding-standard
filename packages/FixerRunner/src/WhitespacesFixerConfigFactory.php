<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner;

use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Exception\Configuration\WhitespaceConfigurationException;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class WhitespacesFixerConfigFactory
{
    /**
     * @var string
     */
    private const TWO_SPACES = '  ';

    /**
     * @var string
     */
    private const FOUR_SPACES = '    ';

    /**
     * @var string
     */
    private const ONE_TAB = '	';

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
        if ($indentation === 'tab' || $indentation === self::ONE_TAB) {
            return self::ONE_TAB;
        }

        if ($indentation === self::TWO_SPACES) {
            return self::TWO_SPACES;
        }

        if ($indentation === 'spaces' || $indentation === self::FOUR_SPACES) {
            return self::FOUR_SPACES;
        }

        $allowedValues = ['tab', 'spaces', self::TWO_SPACES, self::FOUR_SPACES, self::ONE_TAB];

        throw new WhitespaceConfigurationException(sprintf(
            'Value "%s" is not supported in "parameters > indentation".%sUse one of: "%s".',
            $indentation,
            PHP_EOL,
            implode('", "', $allowedValues)
        ));
    }
}
