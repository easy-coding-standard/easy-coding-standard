<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class CheckerTolerantYamlFileLoaderTest extends TestCase
{
    /**
     * @dataProvider provideConfigToConfiguredMethodAndPropertyDefinition()
     * @param mixed[] $expectedMethodCall
     * @param mixed[] $expectedProperties
     */
    public function test(string $config, string $checker, array $expectedMethodCall, array $expectedProperties): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig($config);
        $this->assertTrue($containerBuilder->has($checker));

        $checkerDefinition = $containerBuilder->getDefinition($checker);

        if (count($expectedMethodCall)) {
            $this->checkDefinitionForMethodCall($checkerDefinition, $expectedMethodCall);
        }

        if (count($expectedProperties)) {
            $this->assertSame($expectedProperties, $checkerDefinition->getProperties());
        }
    }

    /**
     * @return mixed[][]
     */
    public function provideConfigToConfiguredMethodAndPropertyDefinition(): array
    {
        return [
            [
                # config
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config.yml',
                # checkers
                ArraySyntaxFixer::class,
                # expected method call
                ['configure', [['syntax' => 'short']]],
                # expected set properties
                [],
            ],
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-imports.yml',
                ArraySyntaxFixer::class,
                ['configure', [['syntax' => 'short']]],
                [],
            ],
            # "@" escaping
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-at.yml',
                LineLengthSniff::class,
                [],
                ['absoluteLineLimit' => '@author'],
            ],
            # keep original keywords
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-classic.yml',
                LineLengthSniff::class,
                [],
                ['absoluteLineLimit' => 150],
            ],
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-classic.yml',
                ArraySyntaxFixer::class,
                ['configure', [['syntax' => 'short']]],
                [],
            ],
            [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-with-bool.yml',
                TypeHintDeclarationSniff::class,
                [],
                ['enableObjectTypeHint' => false],
            ],
        ];
    }

    /**
     * @dataProvider provideConfigToParametersDefinition()
     * @param mixed[] $expectedMethodCall
     * @param mixed[] $expectedProperties
     */
    public function testSkipParameters(string $configFile, array $expectedSkip): void
    {
        $containerBuilder = $this->createAndLoadContainerBuilderFromConfig($configFile);

        $skip = $containerBuilder->getParameterBag()->get('skip');

        $this->assertSame($expectedSkip, $skip);
    }

    /**
     * @return mixed[][]
     */
    public function provideConfigToParametersDefinition(): array
    {
        return [
            'parent configuration' => [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-skip.yml',
                [
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.FirstCode' => null,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.SecondCode' => null,
                ],
            ],
            'configuration importing the parent with already defined skip parameters' => [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-skip-with-import.yml',
                [
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.FirstCode' => null,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.SecondCode' => false,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.ThirdCode' => null,
                ],
            ],
            'configuration importing empty parent' => [
                __DIR__ . '/CheckerTolerantYamlFileLoaderSource/config-skip-with-import-empty.yml',
                [
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.FirstCode' => null,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.SecondCode' => false,
                ],
            ],
        ];
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
    private function checkDefinitionForMethodCall(Definition $definition, array $methodCall): void
    {
        $methodCalls = $definition->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertContains(key($methodCall), $methodCalls[0]);

        $this->assertSame($methodCall, $methodCalls[0]);
    }
}
