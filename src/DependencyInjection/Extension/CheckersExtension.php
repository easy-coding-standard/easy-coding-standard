<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
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
            if (count($configuration) && is_a($checkerClass, ConfigurableFixerInterface::class, true)) {
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

    private function ensurePropertyExists(string $sniffClass, string $property): void
    {
        if (property_exists($sniffClass, $property)) {
            return;
        }

        throw new InvalidSniffPropertyException(sprintf(
            // @todo: add "Did you mean?"
            'Property "%s" was not found on "%s" sniff class. Possible typo in its configuration?',
            $property,
            $sniffClass
        ));
    }
}
