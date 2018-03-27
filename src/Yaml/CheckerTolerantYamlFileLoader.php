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
        // parent cannot by overriden fully, because it has many private methods and properties
        parent::load($resource, $type);

        // 1. possibly reload parameters here again with importing as well
        $path = $this->locator->locate($resource);
        $content = $this->loadFile($path);
        $this->container->fileExists($path);

        // empty file
        if ($content === null) {
            return;
        }

        // imports
        $this->parseImportParameters($content, $path);

        // parameters
        if (isset($content[self::PARAMETERS_KEY])) {
            if (!is_array($content[self::PARAMETERS_KEY])) {
                throw new \InvalidArgumentException(sprintf('The "parameters" key should contain an array in %s. Check your YAML syntax.', $path));
            }

            $mergedParameters = $this->merge($content[self::PARAMETERS_KEY], $this->mergeAwareParameterBag->all());

            $this->mergeAwareParameterBag->add($mergedParameters);
        }

        // load overriden parameters from parent method parameters born by correct merge

        // @todo there needs to be way to load imports before parameter loading
        // so we can override imported values with main config - simple :)

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

    private function parseImportParameters(array $content, $file)
    {
        if (! isset($content['imports'])) {
            return;
        }

        if (! is_array($content['imports'])) {
            throw new \InvalidArgumentException(sprintf('The "imports" key should contain an array in %s. Check your YAML syntax.', $file));
        }

        $defaultDirectory = dirname($file);
        foreach ($content['imports'] as $import) {
            if (!is_array($import)) {
                $import = array('resource' => $import);
            }
            if (!isset($import['resource'])) {
                throw new \InvalidArgumentException(sprintf('An import should provide a resource in %s. Check your YAML syntax.', $file));
            }

            $this->setCurrentDir($defaultDirectory);
            $this->import($import['resource'], isset($import['type']) ? $import['type'] : null, isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false, $file);
        }
    }

}
