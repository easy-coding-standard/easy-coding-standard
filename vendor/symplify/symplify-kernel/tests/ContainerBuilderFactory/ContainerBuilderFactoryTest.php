<?php

declare (strict_types=1);
namespace ECSPrefix202209\Symplify\SymplifyKernel\Tests\ContainerBuilderFactory;

use ECSPrefix202209\PHPUnit\Framework\TestCase;
use ECSPrefix202209\Symplify\SmartFileSystem\SmartFileSystem;
use ECSPrefix202209\Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use ECSPrefix202209\Symplify\SymplifyKernel\ContainerBuilderFactory;
final class ContainerBuilderFactoryTest extends TestCase
{
    public function test() : void
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());
        $containerBuilder = $containerBuilderFactory->create([__DIR__ . '/config/some_services.php'], [], []);
        $hasSmartFileSystemService = $containerBuilder->has(SmartFileSystem::class);
        $this->assertTrue($hasSmartFileSystemService);
    }
}
