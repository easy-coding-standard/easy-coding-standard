<?php

declare (strict_types=1);
namespace ECSPrefix20210814\Symplify\Skipper\Bundle;

use ECSPrefix20210814\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210814\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20210814\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210814\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210814\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
