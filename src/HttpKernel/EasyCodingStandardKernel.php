<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HttpKernel;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [
            new EasyCodingStandardBundle(),
            new SymplifyCodingStandardBundle(),
            new ConsoleColorDiffBundle(),
            new SymplifyKernelBundle(),
        ];
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
