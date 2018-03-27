<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CheckerServiceParametersShifter
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string[]
     */
    private $serviceKeywords = [];

    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    public function __construct()
    {
        $this->checkerConfigurationGuardian = new CheckerConfigurationGuardian();

        $this->serviceKeywords = (new ReflectionClass(YamlFileLoader::class))
            ->getStaticProperties()['serviceKeywords'];
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    public function processYaml(array $yaml): array
    {
        $services = $yaml[self::SERVICES_KEY];

        foreach ($services as $checker => $serviceDefinition) {
            if (! $this->isCheckersClass($checker) || empty($serviceDefinition)) {
                continue;
            }

            if (Strings::endsWith($checker, 'Fixer')) {
                $services = $this->processFixer($services, $checker, $serviceDefinition);
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                $services = $this->processSniff($services, $checker, $serviceDefinition);
            }

            // cleanup parameters
            foreach ($serviceDefinition as $key => $value) {
                if ($this->isReservedKey($key)) {
                    continue;
                }

                unset($services[$checker][$key]);
            }
        }

        $yaml[self::SERVICES_KEY] = $services;

        return $yaml;
    }

    private function isCheckersClass(string $checker): bool
    {
        return Strings::endsWith($checker, 'Fixer') || Strings::endsWith($checker, 'Sniff');
    }

    /**
     * @param mixed[] $yaml
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processFixer(array $services, string $checker, array $serviceDefinition): array
    {
        $this->checkerConfigurationGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);

        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $services[$checker]['calls'] = [
                ['configure', [$serviceDefinition]],
            ];
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
        if (is_bool($value) || is_numeric($value)) {
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
}
