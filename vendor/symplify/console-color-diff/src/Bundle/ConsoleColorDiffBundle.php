<?php

namespace Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
