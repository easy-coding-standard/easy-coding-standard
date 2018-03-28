<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\EasyCodingStandard\Yaml\ParameterInImportResolver;

/**
 * @see ParameterInImportResolver
 */
final class ParameterInImportResolverTest extends TestCase
{
    public function test(): void
    {
        $containerBuilder = new ContainerBuilder();
        $delegatingLoader = (new DelegatingLoaderFactory())->createFromContainerBuilderAndDirectory(
            $containerBuilder,
            __DIR__ . '/CheckerTolerantYamlFileLoader'
        );

        $delegatingLoader->load($this->provideConfig());

        $this->assertTrue($containerBuilder->getParameter('it_works'));
    }

    private function provideConfig(): string
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-monorepo.yml';
        }

        return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-split.yml';
    }
}
