<?php

declare (strict_types=1);
namespace ECSPrefix20211027\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20211027\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211027\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20211027\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211027\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211027\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
