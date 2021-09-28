<?php

declare (strict_types=1);
namespace ECSPrefix20210928\Symplify\ComposerJsonManipulator\Bundle;

use ECSPrefix20210928\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210928\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \ECSPrefix20210928\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210928\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210928\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
