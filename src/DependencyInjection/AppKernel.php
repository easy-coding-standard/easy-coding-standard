<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\EasyCodingStandard\Configuration\Loader\NeonLoader;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private const CONFIG_NAME = 'easy-coding-standard.neon';

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        /** @var DelegatingLoader $loader */
        $loader->load(__DIR__ . '/../config/services.yml');

        /** @var LoaderResolver $resolver */
        $resolver = $loader->getResolver();
        $resolver->addLoader(new NeonLoader);

        $localConfig = getcwd() . '/' . self::CONFIG_NAME;
        if (file_exists($localConfig)) {
            $loader->load($localConfig);
        }
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_easy_coding_standard_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_easy_coding_standard_log';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
