<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class CheckerTolerantYamlFileLoaderTest extends TestCase
{
    /**
     * @dataProvider provideConfigToConfiguredMethodAndParameterDefinition()
     * @param mixed[] $expectedMethodCall
     */
    public function testBare(string $config, string $checker, array $expectedMethodCall): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig($config);
        $this->assertTrue($containerBuilder->has($checker));

        $checkerDefinition = $containerBuilder->getDefinition($checker);
        $this->checkHasMethodCall($checkerDefinition, $expectedMethodCall);
    }

    private function createAndLoadContainerBuilderFromConfig(string $config): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $yamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(dirname($config)));
        $yamlFileLoader->load($config);

        return $containerBuilder;
    }

    /**
     * @param mixed[] $methodCall
     */
    private function checkHasMethodCall(Definition $definition, array $methodCall): void
    {
        $methodCalls = $definition->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertContains(key($methodCall), $methodCalls[0]);

        $this->assertSame($methodCall, $methodCalls[0]);
    }

    /**
     * @return mixed[][]
     */
    public function provideConfigToConfiguredMethodAndParameterDefinition(): array
    {
        return [
            [
                # config
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config.yml',
                # checkers
                ArraySyntaxFixer::class,
                # expected method call
                ['configure', [['syntax' => 'short']]]
            ],
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-imports.yml',
                ArraySyntaxFixer::class,
                ['configure', [['syntax' => 'short']]]
            ]
        ];
    }
}
