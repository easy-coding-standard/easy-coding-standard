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
        $this->checkerConfigurationNormalizer = new CheckerConfigurationNormalizer();
        $this->checkerTypeValidator = new CheckerTypeValidator();
        $this->checkersExtensionGuardian = new CheckersExtensionGuardian();
        $this->mutualCheckerExcluder = new MutualCheckerExcluder();
        $this->conflictingCheckerGuard = new ConflictingCheckerGuard();
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
            $checkerDefinition->setPublic(true);
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

        $definition->addMethodCall('setWhitespacesConfig', [new Reference(WhitespacesFixerConfig::class)]);
    }

    /**
     * @param mixed[] $checkers
     * @return mixed[]
     */
    private function removeExcludedCheckers(array $checkers, ParameterBagInterface $parameterBag): array
    {
        $excludedCheckers = $this->resolveExcludedCheckersOption($parameterBag);

        $this->checkerTypeValidator->validate($excludedCheckers, 'parameters > exclude_checkers');

        foreach ($excludedCheckers as $excludedChecker) {
            unset($checkers[$excludedChecker]);
        }

        return $checkers;
    }

    /**
     * @return string[]
     */
    private function resolveExcludedCheckersOption(ParameterBagInterface $parameterBag): array
    {
        if ($parameterBag->has(self::EXCLUDE_CHECKERS_OPTION)) {
            return $parameterBag->get(self::EXCLUDE_CHECKERS_OPTION);
        }

        // typo proof
        if ($parameterBag->has('excluded_checkers')) {
            return $parameterBag->get('excluded_checkers');
        }

        return [];
    }
}
