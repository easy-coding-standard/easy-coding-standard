<?php

declare (strict_types=1);
namespace ECSPrefix20210831\Symplify\ComposerJsonManipulator\Bundle;

use ECSPrefix20210831\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210831\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \ECSPrefix20210831\Symfony\Component\HttpKernel\Bundle\Bundle
{
    protected function createContainerExtension() : ?\ECSPrefix20210831\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \ECSPrefix20210831\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
