<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20211002\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211002\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20211002\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211002\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211002\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
