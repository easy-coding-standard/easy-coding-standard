<?php

declare (strict_types=1);
namespace ECSPrefix202209\Symplify\EasyTesting\Kernel;

use ECSPrefix202209\Psr\Container\ContainerInterface;
use ECSPrefix202209\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix202209\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyTestingKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = EasyTestingConfig::FILE_PATH;
        return $this->create($configFiles);
    }
}
