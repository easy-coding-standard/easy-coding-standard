<?php

declare (strict_types=1);
namespace ECSPrefix20210827\Symplify\Skipper\Bundle;

use ECSPrefix20210827\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210827\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20210827\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210827\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210827\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
