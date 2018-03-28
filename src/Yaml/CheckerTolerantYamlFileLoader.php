<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PackageBuilder\Exception\Yaml\InvalidParametersValueException;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Yaml\ParameterInImportResolver;

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
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    /**
     * @var ParametersMerger
     */
    private $parametersMerger;

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var ParameterInImportResolver
     */
    private $parameterInImportResolver;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();
        $this->parametersMerger = new ParametersMerger();
        $this->privatesCaller = new PrivatesCaller();
        $this->parameterInImportResolver = new ParameterInImportResolver();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * Same as parent, just merging parameters instead overriding them
     *
     * @see https://github.com/Symplify/Symplify/pull/697
     *
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null): void
    {
        $path = $this->locator->locate($resource);

        $content = $this->loadFile($path);

        $this->container->fileExists($path);

        // empty file
        if ($content === null) {
            return;
        }

        $content = $this->parameterInImportResolver->process($content);

        // imports
        // $this->parseImports($content, $path);
        $this->privatesCaller->callPrivateMethod($this, 'parseImports', $content, $path);

        // parameters
        if (isset($content[self::PARAMETERS_KEY])) {
            $this->ensureParametersIsArray($content, $path);

            foreach ($content[self::PARAMETERS_KEY] as $key => $value) {
                // $this->resolveServices($value, $path, true),
                $resolvedValue = $this->privatesCaller->callPrivateMethod(
                    $this,
                    'resolveServices',
                    $value,
                    $path,
                    true
                );

                // only this section is different
                if ($this->container->hasParameter($key)) {
                    $newValue = $this->parametersMerger->merge(
                        $resolvedValue,
                        $this->container->getParameter($key)
                    );

                    $this->container->setParameter($key, $newValue);
                } else {
                    $this->container->setParameter($key, $resolvedValue);
                }
            }
        }

        // extensions
        // $this->loadFromExtensions($content);
        $this->privatesCaller->callPrivateMethod($this, 'loadFromExtensions', $content);

        // services - not accessible, private parent properties, luckily not needed
        // $this->anonymousServicesCount = 0;
        // $this->anonymousServicesSuffix = ContainerBuilder::hash($path);
        $this->setCurrentDir(dirname($path));
        try {
            // $this->parseDefinitions($content, $path);
            $this->privatesCaller->callPrivateMethod($this, 'parseDefinitions', $content, $path);
        } finally {
            $this->instanceof = [];
        }
    }

    /**
     * Handles checker parameters
     *
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

    /**
     * @param mixed[] $content
     */
    private function ensureParametersIsArray(array $content, string $path): void
    {
        if (is_array($content[self::PARAMETERS_KEY])) {
            return;
        }

        throw new InvalidParametersValueException(sprintf(
            'The "parameters" key should contain an array in "%s". Check your YAML syntax.',
            $path
        ));
    }
}
