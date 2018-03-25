<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerTolerantYamlFileLoader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\EasyCodingStandard\Yaml\CheckerTolerantYamlFileLoader;

final class ParametersMergeTest extends TestCase
{
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
                __DIR__ . '/ParametersSource/config-skip.yml',
                [
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.FirstCode' => null,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.SecondCode' => null,
                ],
            ],
            'configuration importing the parent with already defined skip parameters' => [
                __DIR__ . '/ParametersSource/config-skip-with-import.yml',
                [
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.FirstCode' => null,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.SecondCode' => false,
                    'PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff.ThirdCode' => null,
                ],
            ],
            'configuration importing empty parent' => [
                __DIR__ . '/ParametersSource/config-skip-with-import-empty.yml',
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
}
