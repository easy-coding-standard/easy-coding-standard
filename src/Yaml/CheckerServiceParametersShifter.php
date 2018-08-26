<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Before:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          syntax: short
 *      # sniff
 *      ArraySniff:
 *          syntax: short
 *
 * After:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          calls:
 *              - ['configure', [['syntax' => 'short']]
 *      # sniff
 *      ArraySniff:
 *          parameters:
 *              $syntax: 'short'
 */
final class CheckerServiceParametersShifter
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    /**
     * @var string[]
     */
    private $serviceKeywords = [];

    public function __construct()
    {
        $this->checkerConfigurationGuardian = new CheckerConfigurationGuardian();

        $this->serviceKeywords = (new ReflectionClass(YamlFileLoader::class))
            ->getStaticProperties()['serviceKeywords'];
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    public function process(array $configuration): array
    {
        if (! isset($configuration[self::SERVICES_KEY]) || ! is_array($configuration[self::SERVICES_KEY])) {
            return $configuration;
        }

        $configuration[self::SERVICES_KEY] = $this->processServices($configuration[self::SERVICES_KEY]);

        return $configuration;
    }

    private function isCheckerClass(string $checker): bool
    {
        return Strings::endsWith($checker, 'Fixer') || Strings::endsWith($checker, 'Sniff');
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processFixer(array $services, string $checker, array $serviceDefinition): array
    {
        $this->checkerConfigurationGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);

        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            // fixes comment extra bottom space
            if ($checker === HeaderCommentFixer::class) {
                if (isset($serviceDefinition['header'])) {
                    $serviceDefinition['header'] = trim($serviceDefinition['header']);
                }
            }

            $services[$checker]['calls'] = [['configure', [$serviceDefinition]]];
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processSniff(array $services, string $checker, array $serviceDefinition): array
    {
        // move parameters to property setters
        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $this->checkerConfigurationGuardian->ensurePropertyExists($checker, $key);
            $services[$checker]['properties'][$key] = $this->escapeValue($value);
        }

        return $services;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value)
    {
        if (! is_array($value) && ! is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $key => $nestedValue) {
                $value[$key] = $this->escapeValue($nestedValue);
            }

            return $value;
        }

        return Strings::replace($value, '#@#', '@@');
    }

    /**
     * @param string|int|bool $key
     */
    private function isReservedKey($key): bool
    {
        if (! is_string($key)) {
            return false;
        }

        return in_array($key, $this->serviceKeywords, true);
    }

    /**
     * @param mixed[] $services
     * @return mixed[]
     */
    private function processServices(array $services): array
    {
        foreach ($services as $serviceName => $serviceDefinition) {
            if (! $this->isCheckerClass($serviceName) || empty($serviceDefinition)) {
                continue;
            }

            if (Strings::endsWith($serviceName, 'Fixer')) {
                $services = $this->processFixer($services, $serviceName, $serviceDefinition);
            }

            if (Strings::endsWith($serviceName, 'Sniff')) {
                $services = $this->processSniff($services, $serviceName, $serviceDefinition);
            }

            // cleanup parameters
            $services = $this->cleanupParameters($services, $serviceDefinition, $serviceName);
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function cleanupParameters(array $services, array $serviceDefinition, string $serviceName): array
    {
        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            unset($services[$serviceName][$key]);
        }

        return $services;
    }
}
