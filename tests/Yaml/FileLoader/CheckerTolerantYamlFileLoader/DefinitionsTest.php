<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

use Iterator;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\CodingStandard\Sniffs\DependencyInjection\NoClassInstantiationSniff;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\EasyCodingStandard\Error\Error;

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

        if (count($expectedMethodCall)) {
            $this->checkDefinitionForMethodCall($checkerDefinition, $expectedMethodCall);
        }

        if (count($expectedProperties)) {
            $this->assertSame($expectedProperties, $checkerDefinition->getProperties());
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
            ['absoluteLineLimit' => '@author'],
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
            TypeHintDeclarationSniff::class,
            [],
            ['enableObjectTypeHint' => false],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/checkers.yaml',
            TypeHintDeclarationSniff::class,
            [],
            [
                'enableObjectTypeHint' => false,
            ],
        ];
        yield [
            __DIR__ . '/DefinitionsSource/checkers.yaml',
            NoClassInstantiationSniff::class,
            [],
            [
                'extraAllowedClasses' => [
                    Error::class,
                    'Symplify\PackageBuilder\Reflection\*',
                    ContainerBuilder::class,
                    'Symplify\EasyCodingStandard\Yaml\*',
                    ParameterBag::class,
                ],
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
        $this->assertContains(key($methodCall), $methodCalls[0]);

        $this->assertSame($methodCall, $methodCalls[0]);
    }
}
