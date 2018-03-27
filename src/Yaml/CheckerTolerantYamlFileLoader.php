<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * The need: https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
final class CheckerTolerantYamlFileLoader extends YamlFileLoader
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string
     */
    private const CALLS_KEY = 'calls';

    /**
     * @var string
     */
    private const PROPERTIES_KEY = 'properties';

    /**
     * @var string
     */
    private const PARAMETERS_KEY = 'parameters';

    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    /**
     * @var string[]
     */
    private $serviceKeywords = [];

    /**
     * @var ParameterBag
     */
    private $mergeAwareParameterBag;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerConfigurationGuardian = new CheckerConfigurationGuardian();
        $this->mergeAwareParameterBag = new ParameterBag();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * This method override is needed, so imported parameter values are not overrided by child ones
     *
     * @see https://github.com/Symplify/Symplify/pull/697
     *
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null): void
    {
        parent::load($resource, $type);

        // load overriden parameters from parent method parameters born by correct merge
        $this->container->getParameterBag()->add($this->mergeAwareParameterBag->all());
    }

    /**
     * Merges configurations. Left has higher priority than right one.
     *
     * @autor David Grudl (https://davidgrudl.com)
     * @source https://github.com/nette/di/blob/8eb90721a131262f17663e50aee0032a62d0ef08/src/DI/Config/Helpers.php#L31
     *
     * @param mixed $left
     * @param mixed $right
     * @return mixed[]|string
     */
    public function merge($left, $right)
    {
        if (is_array($left) && is_array($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (isset($right[$key])) {
                        $val = $this->merge($val, $right[$key]);
                    }
                    $right[$key] = $val;
                }
            }
            return $right;
        } elseif ($left === null && is_array($right)) {
            return $right;
        }

        return $left;
    }

    /**
     * @param string $file
     * @return array|mixed|mixed[]
     */
    protected function loadFile($file)
    {
        $decodedYaml = parent::loadFile($file);

        if (isset($decodedYaml[self::PARAMETERS_KEY])) {
            $this->loadParameters((array) $decodedYaml[self::PARAMETERS_KEY], $file);
        }

        if (isset($decodedYaml[self::SERVICES_KEY])) {
            return $this->moveArgumentsToPropertiesOrMethodCalls($decodedYaml);
        }

        return $decodedYaml;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    private function moveArgumentsToPropertiesOrMethodCalls(array $yaml): array
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

            $yaml[self::SERVICES_KEY][$checker][self::CALLS_KEY] = [
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
            $yaml[self::SERVICES_KEY][$checker][self::PROPERTIES_KEY][$key] = $this->escapeValue($value);
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

        return in_array($key, $this->getServiceKeywords(), true);
    }

    /**
     * @return string[]
     */
    private function getServiceKeywords(): array
    {
        if ($this->serviceKeywords) {
            return $this->serviceKeywords;
        }

        $reflectionClass = new ReflectionClass(YamlFileLoader::class);

        return $this->serviceKeywords = $reflectionClass->getStaticProperties()['serviceKeywords'];
    }

    /**
     * Used from @see YamlFileLoader::load()
     * @param mixed[] $parameters
     */
    private function loadParameters(array $parameters, string $file): void
    {
        if ($this->isRootConfig($file)) {
            $newParameters = (array) $this->merge($parameters, $this->mergeAwareParameterBag->all());

        } else {
            // order matters, if imported, then main goes first, imported second - although import should override the other one
            // for the first case, merge args have to be switched
            $newParameters = (array) $this->merge($this->mergeAwareParameterBag->all(), $parameters);
        }

        $this->mergeAwareParameterBag->add($newParameters);
    }

    private function isRootConfig(string $file): bool
    {
        return $file === __DIR__ . '/../config/config.yml';
    }
}
