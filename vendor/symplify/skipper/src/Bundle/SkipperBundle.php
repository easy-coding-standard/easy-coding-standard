<?php

namespace ECSPrefix20210516\Symplify\Skipper\Bundle;

use ECSPrefix20210516\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210516\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20210516\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210516\Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
