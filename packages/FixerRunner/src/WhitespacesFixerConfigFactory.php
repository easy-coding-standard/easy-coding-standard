<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner;

use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class WhitespacesFixerConfigFactory
{
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
        if ($indentation === 'tab') {
            return self::ONE_TAB;
        }

        return self::FOUR_SPACES;
    }
}
