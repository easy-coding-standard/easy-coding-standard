<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Nette\Utils\ObjectMixin;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\FixerIsNotConfigurableException;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\InvalidSniffPropertyException;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckersExtension extends Extension
{
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

    public function __construct()
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
    }

    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $parameterBag = $containerBuilder->getParameterBag();
        if (! $parameterBag->has(self::NAME)) {
            return;
        }

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
            $checkerDefinition = $this->setupCheckerConfiguration($checkerDefinition, $configuration);
            $containerBuilder->setDefinition($checkerClass, $checkerDefinition);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function setupCheckerConfiguration(Definition $checkerDefinition, array $configuration): Definition
    {
        $checkerClass = $checkerDefinition->getClass();

        if (is_a($checkerClass, FixerInterface::class, true)) {
            if (count($configuration)) {
                $this->ensureFixerIsConfigurable($checkerClass, $configuration);
                $checkerDefinition->addMethodCall('configure', [$configuration]);
            }
        } elseif (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $property => $value) {
                $this->ensurePropertyExists($checkerClass, $property);
                $checkerDefinition->setProperty($property, $value);
            }
        }

        return $checkerDefinition;
    }

    /**
     * @param mixed[] $configuration
     */
    private function ensureFixerIsConfigurable(string $fixerClass, array $configuration): void
    {
        if (is_a($fixerClass, ConfigurationDefinitionFixerInterface::class, true)) {
            return;
        }

        throw new FixerIsNotConfigurableException(sprintf(
            'Fixer "%s" is not configurable with configuration: %s.',
            $fixerClass,
            json_encode($configuration)
        ));
    }

    private function ensurePropertyExists(string $sniffClass, string $property): void
    {
        if (property_exists($sniffClass, $property)) {
            return;
        }

        $suggested = ObjectMixin::getSuggestion(array_keys(get_class_vars($sniffClass)), $property);

        throw new InvalidSniffPropertyException(sprintf(
            'Property "%s" was not found on "%s" sniff class in configuration. %s',
            $property,
            $sniffClass,
            $suggested ? sprintf('Did you mean "%s"?', $suggested) : ''
        ));
    }
}
