<?php

declare (strict_types=1);
namespace ECSPrefix20211007\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20211007\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211007\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20211007\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211007\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211007\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
