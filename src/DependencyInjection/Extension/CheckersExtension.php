<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConflictingCheckerGuard;
use Symplify\EasyCodingStandard\Configuration\MutualCheckerExcluder;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckersExtension extends Extension
{
    /**
     * @var string
     */
    private const EXCLUDE_CHECKERS_OPTION = 'exclude_checkers';

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
    private $checkerConfigurationNormalizer;

    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    /**
     * @var CheckersExtensionGuardian
     */
    private $checkersExtensionGuardian;

    /**
     * @var MutualCheckerExcluder
     */
    private $mutualCheckerExcluder;

    /**
     * @var ConflictingCheckerGuard
     */
    private $conflictingCheckerGuard;

    public function __construct()
    {
        $this->checkerConfigurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
        $this->checkersExtensionGuardian = new CheckersExtensionGuardian;
        $this->mutualCheckerExcluder = new MutualCheckerExcluder;
        $this->conflictingCheckerGuard = new ConflictingCheckerGuard;
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

        $checkersConfiguration = $parameterBag->has(self::NAME) ? (array) $parameterBag->get(self::NAME) : [];
        $checkers = $this->checkerConfigurationNormalizer->normalize($checkersConfiguration);

        $this->checkerTypeValidator->validate(array_keys($checkers), 'parameters > checkers');

        $checkers = $this->removeExcludedCheckers($checkers, $parameterBag);

        $checkers = $this->mutualCheckerExcluder->processCheckers($checkers);

        $this->conflictingCheckerGuard->processCheckers($checkers);

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
        if ($checkerClass === null) {
            return;
        }

        if (is_a($checkerClass, FixerInterface::class, true)) {
            $this->checkersExtensionGuardian->ensureFixerIsConfigurable($checkerClass, $configuration);
            $checkerDefinition->addMethodCall('configure', [$configuration]);
        }

        if (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $property => $value) {
                $this->checkersExtensionGuardian->ensurePropertyExists($checkerClass, $property);
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

    /**
     * @param mixed[] $checkers
     * @return mixed[]
     */
    private function removeExcludedCheckers(array $checkers, ParameterBagInterface $parameterBag): array
    {
        $excludedCheckers = $parameterBag->has(self::EXCLUDE_CHECKERS_OPTION)
            ? (array) $parameterBag->get(self::EXCLUDE_CHECKERS_OPTION) : [];

        $this->checkerTypeValidator->validate(array_keys($checkers), 'parameters > exclude_checkers');

        foreach ($excludedCheckers as $excludedChecker) {
            unset($checkers[$excludedChecker]);
        }

        return $checkers;
    }
}
