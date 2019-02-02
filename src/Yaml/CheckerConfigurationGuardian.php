<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Json;
use Nette\Utils\ObjectHelpers;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\FixerIsNotConfigurableException;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\InvalidSniffPropertyException;

final class CheckerConfigurationGuardian
{
    /**
     * @param mixed[] $configuration
     */
    public function ensureFixerIsConfigurable(string $fixerClass, array $configuration): void
    {
        if (is_a($fixerClass, ConfigurableFixerInterface::class, true)) {
            return;
        }

        throw new FixerIsNotConfigurableException(sprintf(
            'Fixer "%s" is not configurable with configuration: %s.',
            $fixerClass,
            Json::encode($configuration)
        ));
    }

    public function ensurePropertyExists(string $sniffClass, string $property): void
    {
        if (property_exists($sniffClass, $property)) {
            return;
        }

        if (! class_exists($sniffClass)) {
            throw new InvalidSniffPropertyException(sprintf(
                'Checker class "%s" in configuration was not found',
                $sniffClass
            ));
        }

        $suggested = ObjectHelpers::getSuggestion(array_keys(get_class_vars($sniffClass)), $property);

        throw new InvalidSniffPropertyException(sprintf(
            'Property "%s" was not found on "%s" sniff class in configuration. %s',
            $property,
            $sniffClass,
            $suggested ? sprintf('Did you mean "%s"?', $suggested) : ''
        ));
    }
}
