<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ComposerJsonManipulator\Bundle;

use ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\Bundle;
use ConfigTransformer20210601\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends \ConfigTransformer20210601\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ConfigTransformer20210601\Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ComposerJsonManipulatorExtension();
    }
}
