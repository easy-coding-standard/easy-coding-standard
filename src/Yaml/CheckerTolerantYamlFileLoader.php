<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Yaml\AbstractParameterMergingYamlFileLoader;
use Symplify\PackageBuilder\Yaml\ParameterInImportResolver;

final class CheckerTolerantYamlFileLoader extends AbstractParameterMergingYamlFileLoader
{
    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    /**
     * @var ParameterInImportResolver
     */
    private $parameterInImportResolver;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();
        $this->parameterInImportResolver = new ParameterInImportResolver();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param string $file
     * @return array|mixed|mixed[]
     */
    protected function loadFile($file)
    {
        $configuration = parent::loadFile($file);
        if ($configuration === null) {
            return [];
        }

        $configuration = $this->checkerServiceParametersShifter->process($configuration);

        return $this->parameterInImportResolver->process($configuration);
    }
}
