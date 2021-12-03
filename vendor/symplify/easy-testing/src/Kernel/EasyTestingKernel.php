<?php

declare (strict_types=1);
namespace ECSPrefix20211203\Symplify\EasyTesting\Kernel;

use ECSPrefix20211203\Psr\Container\ContainerInterface;
use ECSPrefix20211203\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix20211203\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20211203\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs($configFiles) : \ECSPrefix20211203\Psr\Container\ContainerInterface
    {
        $configFiles[] = \ECSPrefix20211203\Symplify\EasyTesting\ValueObject\EasyTestingConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
