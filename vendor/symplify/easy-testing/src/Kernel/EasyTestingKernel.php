<?php

declare (strict_types=1);
namespace ECSPrefix202212\Symplify\EasyTesting\Kernel;

use ECSPrefix202212\Psr\Container\ContainerInterface;
use ECSPrefix202212\Symplify\EasyTesting\ValueObject\EasyTestingConfig;
use ECSPrefix202212\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
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
