<?php

declare (strict_types=1);
namespace ECSPrefix202211\Symplify\EasyTesting\Kernel;

use ECSPrefix202211\Psr\Container\ContainerInterface;
use ECSPrefix202211\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix202211\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
