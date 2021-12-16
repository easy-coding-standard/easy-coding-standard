<?php

declare (strict_types=1);
namespace ECSPrefix20211216\Symplify\EasyTesting\Kernel;

use ECSPrefix20211216\Psr\Container\ContainerInterface;
use ECSPrefix20211216\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix20211216\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends \ECSPrefix20211216\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20211216\Psr\Container\ContainerInterface
    {
        $configFiles[] = \ECSPrefix20211216\Symplify\EasyTesting\ValueObject\EasyTestingConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
