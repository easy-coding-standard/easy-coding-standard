<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel;

use ECSPrefix20211002\Nette\Utils\FileSystem;
use ECSPrefix20211002\Symfony\Component\Config\Loader\DelegatingLoader;
use ECSPrefix20211002\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211002\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20211002\Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use ECSPrefix20211002\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use ECSPrefix20211002\Symplify\Skipper\Bundle\SkipperBundle;
use ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use ECSPrefix20211002\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\Tests\HttpKernel\EasyCodingStandardKernelTest
 */
final class EasyCodingStandardKernel extends \ECSPrefix20211002\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles() : iterable
    {
        return [new \Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle(), new \Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle(), new \ECSPrefix20211002\Symplify\ConsoleColorDiff\Bundle\ConsoleColorDiffBundle(), new \ECSPrefix20211002\Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle(), new \ECSPrefix20211002\Symplify\Skipper\Bundle\SkipperBundle()];
    }
    public function getCacheDir() : string
    {
        return \sys_get_temp_dir() . '/ecs_' . \get_current_user();
    }
    public function getLogDir() : string
    {
        $logDirectory = \sys_get_temp_dir() . '/ecs_log_' . \get_current_user();
        if (\Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
            $logDirectory .= '_' . \Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION;
        }
        return $logDirectory;
    }
    public function boot() : void
    {
        $cacheDir = $this->getCacheDir();
        try {
            \ECSPrefix20211002\Nette\Utils\FileSystem::delete($cacheDir);
            \ECSPrefix20211002\Nette\Utils\FileSystem::createDir($cacheDir);
        } catch (\Throwable $exception) {
            // the "@" is required for parallel run to avoid deleting locked directory
            // Rebuild the container on each run
        }
        parent::boot();
    }
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    protected function prepareContainer($containerBuilder) : void
    {
        // works better with workers - see https://github.com/symfony/symfony/pull/32581
        $containerBuilder->setParameter('container.dumper.inline_factories', \true);
        parent::prepareContainer($containerBuilder);
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader($container) : \ECSPrefix20211002\Symfony\Component\Config\Loader\DelegatingLoader
    {
        $delegatingLoaderFactory = new \Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
