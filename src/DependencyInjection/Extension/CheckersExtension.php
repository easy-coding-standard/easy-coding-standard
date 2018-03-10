<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\ArrayMerger;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckersExtension extends Extension
{
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

    public function __construct()
    {
        $this->checkerConfigurationNormalizer = new CheckerConfigurationNormalizer();
        $this->checkerTypeValidator = new CheckerTypeValidator();
        $this->checkersExtensionGuardian = new CheckersExtensionGuardian();
    }

    /**
     * @param string[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        // remove empty sections
        $configs = array_filter($configs);

        if (! count($configs)) {
            return;
        }

        $checkersConfiguration = ArrayMerger::mergeRecursively($configs);
        $checkers = $this->checkerConfigurationNormalizer->normalize($checkersConfiguration);

        $this->checkerTypeValidator->validate(array_keys($checkers), 'checkers');

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
            // clean merge null values leftover, e.g. when parent checkers has `~`, but later has `[]`
            // skip empty configs
            if (empty($configuration)) {
                return;
            }

            $this->checkersExtensionGuardian->ensureFixerIsConfigurable($checkerClass, $configuration);
            $checkerDefinition->addMethodCall('configure', [$configuration]);
            return;
        }

        if (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $property => $value) {
                $this->checkersExtensionGuardian->ensurePropertyExists($checkerClass, $property);
                $checkerDefinition->setProperty($property, $value);
            }
        }
    }
}
