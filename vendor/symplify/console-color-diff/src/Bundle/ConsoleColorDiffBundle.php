<?php

declare (strict_types=1);
namespace ECSPrefix20210915\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210915\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210915\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210915\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210915\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210915\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
