<?php

declare (strict_types=1);
namespace ECSPrefix20210823\Symplify\Skipper\Bundle;

use ECSPrefix20210823\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210823\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20210823\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210823\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210823\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
