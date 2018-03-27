<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerTolerantYamlFileLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\EasyCodingStandard\DependencyInjection\EasyCodingStandardKernel;

final class CurrentWorkingDirectoryTest extends TestCase
{
    public function test(): void
    {
        $containerBuilder = new ContainerBuilder();
        $delegatingLoader = (new DelegatingLoaderFactory())->createFromContainerBuilderAndKernel(
            $containerBuilder,
            new EasyCodingStandardKernel()
        );

        $delegatingLoader->load(__DIR__ . '/CurrentWorkingDirectorySource/config-with-import-param.yml');

        $this->assertArrayHasKey('skip', $containerBuilder->getParameterBag()->all());
    }
}
