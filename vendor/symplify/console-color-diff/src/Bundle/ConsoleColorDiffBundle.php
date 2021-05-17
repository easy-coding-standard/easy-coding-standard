<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210517\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210517\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210517\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210517\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
