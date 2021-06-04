<?php

declare (strict_types=1);
namespace ECSPrefix20210604\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210604\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210604\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210604\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210604\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
