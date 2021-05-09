<?php

namespace Symplify\ConsoleColorDiff\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;

final class ConsoleColorDiffBundle extends Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new ConsoleColorDiffExtension();
    }
}
