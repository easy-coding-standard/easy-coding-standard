<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HttpKernel;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\CodingStandard\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\EasyCodingStandard\EasyCodingStandardBundle;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\ParameterNameGuard\ParameterNameGuardBundle;

final class EasyCodingStandardKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/easy_coding_standard';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/easy_coding_standard_logs';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [
            new EasyCodingStandardBundle(),
            new SymplifyCodingStandardBundle(),
            new ConsoleColorDiffBundle(),
            new ParameterNameGuardBundle(),
        ];
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $delegatingLoaderFactory = new DelegatingLoaderFactory();

        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
