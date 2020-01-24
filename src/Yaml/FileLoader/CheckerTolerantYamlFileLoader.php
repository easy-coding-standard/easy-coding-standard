<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml\FileLoader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Yaml\CheckerServiceParametersShifter;
use Symplify\PackageBuilder\Yaml\FileLoader\AbstractParameterMergingYamlFileLoader;

final class CheckerTolerantYamlFileLoader extends AbstractParameterMergingYamlFileLoader
{
    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param string $file
     * @return mixed|mixed[]
     */
    protected function loadFile($file)
    {
        /** @var mixed[]|null $configuration */
        $configuration = parent::loadFile($file);
        if ($configuration === null) {
            return [];
        }

        return $this->checkerServiceParametersShifter->process($configuration);
    }
}
