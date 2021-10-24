<?php

declare (strict_types=1);
namespace ECSPrefix20211024\Symplify\ComposerJsonManipulator\Bundle;

use ECSPrefix20211024\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211024\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \ECSPrefix20211024\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211024\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211024\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
