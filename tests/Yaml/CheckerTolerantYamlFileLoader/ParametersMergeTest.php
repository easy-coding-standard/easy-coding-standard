<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerTolerantYamlFileLoader;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

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
            __DIR__ . '/ParametersSource/config-skip-with-import.yml',
            [
                'skip' => [
                    'firstCode' => null,
                    'secondCode' => false,
                    'thirdCode' => null,
                ]
            ],
            'configuration importing the parent with already defined skip parameters',
        ];

        yield [
            __DIR__ . '/ParametersSource/config-skip-with-import-empty.yml',
            [
                'skip' => [
                    'firstCode' => null,
                    'secondCode' => null,
                ]
            ],
            'configuration importing empty import',
        ];
    }

    private function createAndLoadContainerBuilderFromConfig(string $config): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $yamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(dirname($config)));
        $yamlFileLoader->load($config);

        return $containerBuilder;
    }
}
