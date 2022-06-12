<?php

declare (strict_types=1);
namespace ECSPrefix20220612\Symplify\EasyTesting\Kernel;

use ECSPrefix20220612\Psr\Container\ContainerInterface;
use ECSPrefix20220612\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix20220612\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
