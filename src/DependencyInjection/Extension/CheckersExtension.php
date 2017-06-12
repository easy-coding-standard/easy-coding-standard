<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\CheckerFilter;
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

    /**
     * @var CheckerFilter
     */
    private $checkerFilter;

    public function __construct()
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
        $this->checkerFilter = new CheckerFilter;
    }

    /**
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $checkersConfiguration = $containerBuilder->getParameterBag()
            ->get(self::NAME);
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
            if (is_a($checkerClass, FixerInterface::class, true)) {
                $fixerDefinition = $this->createFixerDefinition($checkerClass, $configuration);
                $containerBuilder->setDefinition($checkerClass, $fixerDefinition);
            } elseif (is_a($checkerClass, Sniff::class, true)) {
                $sniffDefinition = $this->createSniffDefinition($checkerClass, $configuration);
                $containerBuilder->setDefinition($checkerClass, $sniffDefinition);
            }
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function createFixerDefinition(string $fixerClass, array $configuration): Definition
    {
        $fixerDefinition = new Definition($fixerClass);

        if (count($configuration) && is_a($fixerClass, ConfigurableFixerInterface::class, true)) {
            $fixerDefinition->addMethodCall('configure', [$configuration]);
        }

        return $fixerDefinition;
    }

    /**
     * @param mixed[] $configuration
     */
    private function createSniffDefinition(string $sniffClass, array $configuration): Definition
    {
        $sniffDefinition = new Definition($sniffClass);

        foreach ($configuration as $property => $value) {
            $this->ensurePropertyExists($sniffClass, $property);

            // Is escape value needed? Reference class is required in Symfony
            $sniffDefinition->setProperty($property, $value);
        }

        return $sniffDefinition;
    }

//    /**
//     * @param mixed $value
//     * @return mixed
//     */
//    private function escapeValue($value)
//    {
//        if (is_array($value)) {
//            return $this->escapeAtSignInArray($value);
//        }
//
//        if (is_string($value)) {
//            return $this->escapeAtSign($value);
//        }
//
//        return $value;
//    }

//    /**
//     * @param mixed[] $value
//     * @return mixed[]
//     */
//    private function escapeAtSignInArray(array $value): array
//    {
//        foreach ($value as $key => $subValue) {
//            if (is_string($subValue)) {
//                $value[$key] = $this->escapeAtSign($subValue);
//            }
//        }
//
//        return $value;
//    }

//    private function escapeAtSign(string $value): string
//    {
//        if (Strings::startsWith($value, '@')) {
//            return '@' . $value;
//        }
//
//        return $value;
//    }

    private function ensurePropertyExists(string $sniffClass, string $property): void
    {
        if (property_exists($sniffClass, $property)) {
            return;
        }

        throw new InvalidSniffPropertyException(sprintf(
            'Property "%s" was not found on "%s" sniff class. Possible typo in its configuration?',
            $property,
            $sniffClass
        ));
    }
}
