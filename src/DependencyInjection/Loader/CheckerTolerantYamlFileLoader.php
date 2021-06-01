<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\DependencyInjection\Loader;

use ConfigTransformer20210601\Symfony\Component\Config\FileLocatorInterface;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
use ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Yaml\CheckerServiceParametersShifter;
/**
 * @see https://github.com/symplify/config-transformer/commit/0244abf3953eb0c5578d203b75749545f705c2a3
 */
final class CheckerTolerantYamlFileLoader extends \ConfigTransformer20210601\Symfony\Component\DependencyInjection\Loader\YamlFileLoader
{
    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;
    public function __construct(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, \ConfigTransformer20210601\Symfony\Component\Config\FileLocatorInterface $fileLocator)
    {
        $this->checkerServiceParametersShifter = new \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Yaml\CheckerServiceParametersShifter();
        parent::__construct($containerBuilder, $fileLocator);
    }
    /**
     * @param string $file
     * @return mixed[]
     */
    protected function loadFile($file) : array
    {
        /** @var mixed[]|null $configuration */
        $configuration = parent::loadFile($file);
        if ($configuration === null) {
            return [];
        }
        return $this->checkerServiceParametersShifter->process($configuration);
    }
}
