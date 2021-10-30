<?php

declare (strict_types=1);
namespace ECSPrefix20211030\Symplify\ComposerJsonManipulator\Bundle;

use ECSPrefix20211030\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20211030\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \ECSPrefix20211030\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20211030\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20211030\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
