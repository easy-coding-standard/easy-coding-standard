<?php

declare (strict_types=1);
namespace ECSPrefix20220512\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use ECSPrefix20220512\PHPUnit\Framework\TestCase;
use ECSPrefix20220512\Symplify\SmartFileSystem\SmartFileSystem;
use ECSPrefix20220512\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix20220512\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends \ECSPrefix20220512\PHPUnit\Framework\TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new \ECSPrefix20220512\Symplify\SymplifyKernel\ContainerBuilderFactory(new \ECSPrefix20220512\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(\ECSPrefix20220512\Symplify\SmartFileSystem\SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
