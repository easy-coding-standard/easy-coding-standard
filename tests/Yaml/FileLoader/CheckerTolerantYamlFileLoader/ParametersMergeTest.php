<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;

final class ParametersMergeTest extends TestCase
{
    /**
     * @dataProvider provideConfigToParameters()
     * @param mixed[] $expectedParameters
     */
    public function test(string $configFile, array $expectedParameters, string $message): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig($configFile);

        $this->assertSame($expectedParameters, $containerBuilder->getParameterBag()->all(), $message);
    }

    public function provideConfigToParameters(): Iterator
    {
        yield [
            __DIR__ . '/ParametersSource/config-skip-with-import.yaml',
            [
                'skip' => [
                    'firstCode' => null,
                    'secondCode' => false,
                    'thirdCode' => null,
                ],
            ],
            'import parent with already defined parameters with same keys',
        ];

        yield [
            __DIR__ . '/ParametersSource/config-skip-with-import-empty.yaml',
            [
                'skip' => [
                    'firstCode' => null,
                    'secondCode' => null,
                ],
            ],
            'import empty config',
        ];

        yield [
            __DIR__ . '/ParametersSource/config-string-override.yaml',
            [
                'key' => 'new_value',
            ],
            'override string key',
        ];
    }

    /**
     * Covers bit complicated issue https://github.com/Symplify/Symplify/issues/736
     */
    public function testMainConfigValueOverride(): void
    {
        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = (new DelegatingLoaderFactory())->createContainerBuilderAndConfig(
            $containerBuilder,
            __DIR__ . '/someFile.yaml'
        );

        // local "config/config.yaml"
        $delegatingLoader->load(__DIR__ . '/../../../../config/config.yaml');
        // mimics user's "easy-config-standard.yaml" with own values
        $delegatingLoader->load(__DIR__ . '/ParametersSource/root-config-override.yaml');

        $parameters = $containerBuilder->getParameterBag()->all();
        $this->assertArrayHasKey('cache_directory', $parameters);
        $this->assertSame('new_value', $parameters['cache_directory']);
    }

    private function createAndLoadContainerBuilderFromConfig(string $config): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $delegatingLoader = (new DelegatingLoaderFactory())->createContainerBuilderAndConfig(
            $containerBuilder,
            $config
        );
        $delegatingLoader->load($config);

        return $containerBuilder;
    }
}
