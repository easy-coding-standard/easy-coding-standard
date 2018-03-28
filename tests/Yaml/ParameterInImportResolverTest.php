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

        $delegatingLoader->load($this->provideConfig());

        $this->assertTrue($containerBuilder->getParameter('it_works'));
    }

    /**
     * @return string
     */
    private function provideConfig(): string
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-monorepo.yml';
        } else {
            return __DIR__ . '/ParameterInImportResolverSource/config-with-import-param-split.yml';
        }
    }
}
