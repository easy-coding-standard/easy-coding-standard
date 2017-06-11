<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
        $loader->load(__DIR__ . '/../config/services.yml');

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
        return [
            new CheckersBundle
        ];
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

    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        /** @var DelegatingLoader $delegationLoader */
        $delegationLoader = parent::getContainerLoader($container);

        /** @var LoaderResolver $resolver */
        $resolver = $delegationLoader->getResolver();
        $resolver->addLoader(new NeonLoader);

        return $delegationLoader;
    }
}
