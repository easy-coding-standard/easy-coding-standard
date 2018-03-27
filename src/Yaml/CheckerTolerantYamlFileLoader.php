<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
    private const PARAMETERS_KEY = 'parameters';

    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerConfigurationGuardian = new CheckerConfigurationGuardian();
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();

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
        if (isset($content[self::PARAMETERS_KEY]) && is_array($content[self::PARAMETERS_KEY])) {
            $mergedParameters = $this->merge(
                $content[self::PARAMETERS_KEY],
                $this->container->getParameterBag()->all()
            );

            $this->container->getParameterBag()->add($mergedParameters);
        }

        // @todo there needs to be way to load imports before parameter loading
        // so we can override imported values with main config - simple :)
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

        if (! isset($decodedYaml[self::SERVICES_KEY])) {
            return $decodedYaml;
        }

        $decodedYaml[self::SERVICES_KEY] = $this->checkerServiceParametersShifter->processServices(
            $decodedYaml[self::SERVICES_KEY]
        );

        return $decodedYaml;
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
