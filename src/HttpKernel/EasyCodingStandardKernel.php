<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20210708\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20210708\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210708\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210708\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use ECSPrefix20210708\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Application\Version\VersionResolver;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use ECSPrefix20210708\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20210708\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20210708\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends \ECSPrefix20210708\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return mixed[]
     */
    public function registerBundles()
    {
        return [new \Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle(), new \Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle(), new \ECSPrefix20210708\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle(), new \ECSPrefix20210708\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \ECSPrefix20210708\Symplify\Skipper\Bundle\SkipperBundle()];
    }
    public function getCacheDir() : string
    {
        // the PACKAGE_VERSION constant helps to rebuild cache on new release, but just once
        $cacheDirectory = \sys_get_temp_dir() . '/ecs_' . \get_current_user();
        if (\Symplify\EasyCodingStandard\Application\Version\VersionResolver::PACKAGE_VERSION !== '@package_version@') {
            $cacheDirectory .= '_' . \Symplify\EasyCodingStandard\Application\Version\VersionResolver::PACKAGE_VERSION;
        }
        return $cacheDirectory;
    }
    public function getLogDir() : string
    {
        $logDirectory = \sys_get_temp_dir() . '/ecs_log_' . \get_current_user();
        if (\Symplify\EasyCodingStandard\Application\Version\VersionResolver::PACKAGE_VERSION !== '@package_version@') {
            $logDirectory .= '_' . \Symplify\EasyCodingStandard\Application\Version\VersionResolver::PACKAGE_VERSION;
        }
        return $logDirectory;
    }
    /**
     * @return void
     */
    protected function prepareContainer(\ECSPrefix20210708\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        // works better with workers - see https://github.com/symfony/symfony/pull/32581
        $containerBuilder->setParameter('container.dumper.inline_factories', \true);
        parent::prepareContainer($containerBuilder);
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(\ECSPrefix20210708\Symfony\Component\DependencyInjection\ContainerInterface $container) : \ECSPrefix20210708\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $delegatingLoaderFactory = new \Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
