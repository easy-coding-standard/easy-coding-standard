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
    public function testBare(): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig(
            __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config.yml'
        );

        $this->assertTrue($containerBuilder->has(ArraySyntaxFixer::class));

        $arraySyntaxFixerDefinition = $containerBuilder->getDefinition(ArraySyntaxFixer::class);
        $this->checkHasMethodCall($arraySyntaxFixerDefinition, ['configure', [['syntax' => 'short']]]);
    }

    public function testBareImport(): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig(
            __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-imports.yml'
        );

        $this->assertTrue($containerBuilder->has(ArraySyntaxFixer::class));

        $arraySyntaxFixerDefinition = $containerBuilder->getDefinition(ArraySyntaxFixer::class);
        $this->checkHasMethodCall($arraySyntaxFixerDefinition, ['configure', [['syntax' => 'short']]]);
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
}
