<?php

declare (strict_types=1);
namespace ECSPrefix20220416\Symplify\VendorPatches\Kernel;

use ECSPrefix20220416\Psr\Container\ContainerInterface;
use ECSPrefix20220416\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use ECSPrefix20220416\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class VendorPatchesKernel extends \ECSPrefix20220416\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220416\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = \ECSPrefix20220416\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
