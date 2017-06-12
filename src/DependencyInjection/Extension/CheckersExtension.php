<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\CheckerFilter;
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

        dump($checkers);
//        $this->registerFixersAsServices($fixers);
        die;
        // 1. register as services and
        // 2. split to factories
    }

    /**
     * @param mixed[] $fixers
     */
    private function registerFixersAsServices(array $fixers): void
    {
        $containerBuilder = $this->getContainerBuilder();
        foreach ($fixers as $fixerClass => $configuration) {
            $fixerDefinition = $this->createFixerDefinition($fixerClass, $configuration);
            $containerBuilder->addDefinition(md5($fixerClass), $fixerDefinition);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function createFixerDefinition(string $fixerClass, array $configuration): ServiceDefinition
    {
        $fixerDefinition = new ServiceDefinition;
        $fixerDefinition->setClass($fixerClass);

        // 1. register first one
        if (count($configuration) && is_a($fixerClass, ConfigurableFixerInterface::class, true)) {
            $fixerDefinition->addSetup('configure', [$configuration]);
        }

        // 2. register second one

        return $fixerDefinition;
    }

    /**
     * @param mixed[] $sniffs
     */
    private function registerSniffsAsServices(array $sniffs): void
    {
        $containerBuilder = $this->getContainerBuilder();
        foreach ($sniffs as $sniffClass => $configuration) {
            $sniffDefinition = $this->createSniffDefinition($sniffClass, $configuration);
            $containerBuilder->addDefinition(md5($sniffClass), $sniffDefinition);
        }
    }

    /**
     * @param mixed[] $configuration
     */
    private function createSniffDefinition(string $sniffClass, array $configuration): ServiceDefinition
    {
        $sniffDefinition = new ServiceDefinition;
        $sniffDefinition->setClass($sniffClass);

        foreach ($configuration as $property => $value) {
            $sniffDefinition->addSetup(
                '$' . $property,
                [$this->escapeValue($value)]
            );
        }

        return $sniffDefinition;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value)
    {
        if (is_array($value)) {
            return $this->escapeAtSignInArray($value);
        }

        if (is_string($value)) {
            return $this->escapeAtSign($value);
        }

        return $value;
    }

    /**
     * @param mixed[] $value
     * @return mixed[]
     */
    private function escapeAtSignInArray(array $value): array
    {
        foreach ($value as $key => $subValue) {
            if (is_string($subValue)) {
                $value[$key] = $this->escapeAtSign($subValue);
            }
        }

        return $value;
    }

    private function escapeAtSign(string $value): string
    {
        if (Strings::startsWith($value, '@')) {
            return '@' . $value;
        }

        return $value;
    }
}
