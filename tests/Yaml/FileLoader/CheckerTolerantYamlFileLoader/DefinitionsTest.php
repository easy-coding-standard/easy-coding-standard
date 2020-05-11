<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

use Iterator;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;

final class DefinitionsTest extends TestCase
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

        if (count($expectedMethodCall) > 0) {
            $this->checkDefinitionForMethodCall($checkerDefinition, $expectedMethodCall);
        }
    }

    public function provideConfigToConfiguredMethodAndPropertyDefinition(): Iterator
    {
        yield [
            # config
            __DIR__ . '/DefinitionsSource/config.yaml',
            # checkers
            ArraySyntaxFixer::class,
            # expected method call
            ['configure', [['syntax' => 'short']]],
            # expected set properties
            [],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/config-with-imports.yaml',
            ArraySyntaxFixer::class,
            ['configure', [['syntax' => 'short']]],
            [],
        ];
        # "@" escaping
        yield [
            __DIR__ . '/DefinitionsSource/config-with-at.yaml',
            LineLengthSniff::class,
            [],
            ['absoluteLineLimit' => '@author, @var'],
        ];
        # keep original keywords
        yield [
            __DIR__ . '/DefinitionsSource/config-classic.yaml',
            LineLengthSniff::class,
            [],
            ['absoluteLineLimit' => 150],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/config-classic.yaml',
            ArraySyntaxFixer::class,
            ['configure', [['syntax' => 'short']]],
            [],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/config-with-bool.yaml',
            ParameterTypeHintSniff::class,
            [],
            ['enableObjectTypeHint' => false],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/checkers.yaml',
            ParameterTypeHintSniff::class,
            [],
            [
                'enableObjectTypeHint' => false,
            ],
        ];
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

    /**
     * @param mixed[] $methodCall
     */
    private function checkDefinitionForMethodCall(Definition $definition, array $methodCall): void
    {
        $methodCalls = $definition->getMethodCalls();

        $this->assertCount(1, $methodCalls);

        /** @var string $definitionMethodCallKey */
        $definitionMethodCallKey = key($methodCall);

        $this->assertArrayHasKey($definitionMethodCallKey, $methodCalls[0]);

        $this->assertSame($methodCall, $methodCalls[0]);
    }
}
