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
        foreach ($yaml[self::SERVICES_KEY] as $checker => $serviceDefinition) {
            if (! $this->isCheckersClass($checker) || empty($serviceDefinition)) {
                continue;
            }

            if (Strings::endsWith($checker, 'Fixer')) {
                $yaml = $this->processFixer($yaml, $checker, $serviceDefinition);
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                $yaml = $this->processSniff($yaml, $checker, $serviceDefinition);
            }

            // cleanup parameters
            foreach ($serviceDefinition as $key => $value) {
                if ($this->isReservedKey($key)) {
                    continue;
                }

                unset($yaml[self::SERVICES_KEY][$checker][$key]);
            }
        }

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
    private function processFixer(array $yaml, string $checker, array $serviceDefinition): array
    {
        $this->checkerConfigurationGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);

        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $yaml[self::SERVICES_KEY][$checker]['calls'] = [
                ['configure', [$serviceDefinition]],
            ];
        }

        return $yaml;
    }

    /**
     * @param mixed[] $yaml
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processSniff(array $yaml, string $checker, array $serviceDefinition): array
    {
        // move parameters to property setters
        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $this->checkerConfigurationGuardian->ensurePropertyExists($checker, $key);
            $yaml[self::SERVICES_KEY][$checker]['properties'][$key] = $this->escapeValue($value);
        }

        return $yaml;
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
