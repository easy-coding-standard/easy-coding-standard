<?php

declare (strict_types=1);
namespace ECSPrefix20211029\Symplify\Skipper\Bundle;

use ECSPrefix20211029\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211029\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20211029\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211029\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211029\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
