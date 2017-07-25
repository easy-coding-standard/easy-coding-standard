<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckersExtension extends Extension
{
    /**
     * @var string
     */
    private const SERVICE_NAME_WHITESPACE_CONFIG = 'fixerWhitespaceConfig';

    /**
     * @var string
     */
    private const FOUR_SPACES = '    ';

    /**
     * @var string
     */
    private const ONE_TAB = '	';

    /**
     * @var string
     */
    private const NAME = 'checkers';

    /**
     * @var CheckerConfigurationNormalizer
     */
    private $configurationNormalizer;

    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    /**
     * @var CheckersExtensionGuardian
     */
    private $checkerExtensionGuardian;

    public function __construct()
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
        $this->checkerExtensionGuardian = new CheckersExtensionGuardian;
    }

    /**
     * @param string[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $parameterBag = $containerBuilder->getParameterBag();
        if (! $parameterBag->has(self::NAME)) {
            return;
        }

        $this->registerWhitespacesFixerConfigDefinition($containerBuilder);

        $checkersConfiguration = $parameterBag->get(self::NAME) ?? [];
        $checkers = $this->configurationNormalizer->normalize($checkersConfiguration);
        $this->checkerTypeValidator->validate(array_keys($checkers));
        $this->registerCheckersAsServices($containerBuilder, $checkers);
    }

    /**
     * @param mixed[] $checkers
     */
    private function registerCheckersAsServices(ContainerBuilder $containerBuilder, array $checkers): void
    {
        foreach ($checkers as $checkerClass => $configuration) {
            $checkerDefinition = new Definition($checkerClass);
            $this->setupCheckerConfiguration($checkerDefinition, $configuration);
            $this->setupCheckerWithIndentation($checkerDefinition);
            $containerBuilder->setDefinition($checkerClass, $checkerDefinition);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function setupCheckerConfiguration(Definition $checkerDefinition, array $configuration): void
    {
        if (! count($configuration)) {
            return;
        }

        $checkerClass = $checkerDefinition->getClass();

        if (is_a($checkerClass, FixerInterface::class, true)) {
            $this->checkerExtensionGuardian->ensureFixerIsConfigurable($checkerClass, $configuration);
            $checkerDefinition->addMethodCall('configure', [$configuration]);
        }

        if (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $property => $value) {
                $this->checkerExtensionGuardian->ensurePropertyExists($checkerClass, $property);
                $checkerDefinition->setProperty($property, $value);
            }
        }
    }

    private function setupCheckerWithIndentation(Definition $definition): void
    {
        $checkerClass = $definition->getClass();
        if (! is_a($checkerClass, WhitespacesAwareFixerInterface::class, true)) {
            return;
        }

        $definition->addMethodCall(
            'setWhitespacesConfig',
            [new Reference(self::SERVICE_NAME_WHITESPACE_CONFIG)]
        );
    }

    private function registerWhitespacesFixerConfigDefinition(ContainerBuilder $containerBuilder): void
    {
        $indentation = $this->resolveIndentationValueFromParameter($containerBuilder);

        $whitespacesFixerConfigDefinition = new Definition(
            WhitespacesFixerConfig::class,
            [$indentation, PHP_EOL]
        );
        $containerBuilder->setDefinition(self::SERVICE_NAME_WHITESPACE_CONFIG, $whitespacesFixerConfigDefinition);
    }

    private function resolveIndentationValueFromParameter(ContainerBuilder $containerBuilder): string
    {
        if (! $containerBuilder->hasParameter('indentation')) {
            return self::FOUR_SPACES;
        }

        $indentation = $containerBuilder->getParameter('indentation');
        if ($indentation === 'tab') {
            return self::ONE_TAB;
        }

        return self::FOUR_SPACES;
    }
}
