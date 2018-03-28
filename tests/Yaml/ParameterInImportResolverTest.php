<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\EasyCodingStandard\DependencyInjection\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Yaml\ParameterInImportResolver;

/**
 * @see ParameterInImportResolver
 */
final class ParameterInImportResolverTest extends TestCase
{
    public function test(): void
    {
        $containerBuilder = new ContainerBuilder();
        $delegatingLoader = (new DelegatingLoaderFactory())->createFromContainerBuilderAndKernel(
            $containerBuilder,
            new EasyCodingStandardKernel()
        );

        $delegatingLoader->load(__DIR__ . '/ParameterInImportResolverSource/config-with-import-param.yml');

        $this->assertArrayHasKey('skip', $containerBuilder->getParameterBag()->all());
    }
}
