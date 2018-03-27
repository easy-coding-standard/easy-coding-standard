<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use function get_parent_class;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\Reflection\PrivatesSetter;

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
     * @var PrivatesSetter
     */
    private $privatesSetter;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();
        $this->parametersMerger = new ParametersMerger();
        $this->privatesCaller = new PrivatesCaller();
        $this->privatesSetter = new PrivatesSetter();

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

        // imports
        // $this->parseImports($content, $path);
        $this->privatesCaller->callPrivateMethod($this, 'parseImports', $content, $path);

        // parameters
        if (isset($content[self::PARAMETERS_KEY])) {
            if (! is_array($content[self::PARAMETERS_KEY])) {
                throw new InvalidArgumentException(sprintf('The "parameters" key should contain an array in %s. Check your YAML syntax.', $path));
            }

            foreach ($content[self::PARAMETERS_KEY] as $key => $value) {
                // $this->resolveServices($value, $path, true),
                $resolvedValue = $this->privatesCaller->callPrivateMethod($this, 'resolveServices', $value, $path, true);

                // only this section is different
                if ($this->container->hasParameter($key)) {
                    $newValue = (new ParametersMerger())->merge(
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
            $this->instanceof = array();
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
}
