<?php

declare (strict_types=1);
namespace ECSPrefix20211014\Symplify\Skipper\Bundle;

use ECSPrefix20211014\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211014\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20211014\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211014\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211014\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
