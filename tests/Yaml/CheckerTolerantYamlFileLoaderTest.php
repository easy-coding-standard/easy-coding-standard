<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
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
        $methodCalls = $arraySyntaxFixerDefinition->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertContains('configure', $methodCalls[0]);
        $this->assertSame(['configure', [['syntax' => 'short']]], $methodCalls[0]);
    }

    public function testConfig(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config.yml'
        );

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(1, $fixerFileProcessor->getCheckers());
    }

    public function testConfigWithImports(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-imports.yml'
        );

        /** @var FixerFileProcessor $fixerFileProcessor */
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->assertCount(1, $fixerFileProcessor->getCheckers());
    }

    /**
     * @return ContainerBuilder
     */
    private function createAndLoadContainerBuilderFromConfig(string $config): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $yamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(dirname($config)));
        $yamlFileLoader->load($config);

        return $containerBuilder;
    }
}
