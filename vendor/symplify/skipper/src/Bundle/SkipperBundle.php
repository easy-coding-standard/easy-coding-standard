<?php

namespace Symplify\Skipper\Bundle;

use ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\Skipper\DependencyInjection\Extension\SkipperExtension;
final class SkipperBundle extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \Symplify\Skipper\DependencyInjection\Extension\SkipperExtension();
    }
}
