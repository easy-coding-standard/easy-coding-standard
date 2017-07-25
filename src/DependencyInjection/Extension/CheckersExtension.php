<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Nette\Utils\ObjectMixin;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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
     * @var bool
     */
    private $isWhitespaceFixerConfigRegistered = false;

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

        $this->registerWhitespacesFixerConfigDefinition($containerBuilder);

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
            $this->setupCheckerWithIndentation($checkerDefinition, $containerBuilder);
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
            $this->ensureFixerIsConfigurable($checkerClass, $configuration);
            $checkerDefinition->addMethodCall('configure', [$configuration]);
        } elseif (is_a($checkerClass, Sniff::class, true)) {
            foreach ($configuration as $property => $value) {
                $this->ensurePropertyExists($checkerClass, $property);
                $checkerDefinition->setProperty($property, $value);
            }
        }
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

    private function setupCheckerWithIndentation(Definition $definition, ContainerBuilder $containerBuilder): void
    {
        $checkerClass = $definition->getClass();
        if (! is_a($checkerClass, WhitespacesAwareFixerInterface::class, true)) {
            return;
        }

        $definition->addMethodCall('setWhitespacesConfig', [new Reference('fixerWhitespaceConfig')]);
    }

    private function registerWhitespacesFixerConfigDefinition(ContainerBuilder $containerBuilder): void
    {
        $indentation = $this->resolveIndentationValueFromParameter($containerBuilder);

        $whitespacesFixerConfigDefinition = new Definition(WhitespacesFixerConfig::class, [$indentation, PHP_EOL]);
        $containerBuilder->setDefinition('fixerWhitespaceConfig', $whitespacesFixerConfigDefinition);

        $this->isWhitespaceFixerConfigRegistered = true;
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
