<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\PhpCsFixer\Fixer;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DI\ContainerFactory;
use Symplify\EasyCodingStandard\RuleRunner\Fixer\FixerFactory;

final class FixerFactoryTest extends TestCase
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->fixerFactory = $container->getByType(FixerFactory::class);
    }

    /**
     * @dataProvider provideCreateData
     */
    public function testCreateFromRulesAndExcludedRules(array $fixers, array $excludedRules, int $expectedFixerCount)
    {
        $fixers = $this->fixerFactory->createFromEnabledRulesAndExcludedRules($fixers, $excludedRules);
        $this->assertCount($expectedFixerCount, $fixers);

        if (count($fixers)) {
            $fixer = $fixers[0];
            $this->assertInstanceOf(FixerInterface::class, $fixer);
        }
    }

    public function testRuleConfiguration()
    {
        $rules = $this->fixerFactory->createFromEnabledRulesAndExcludedRules(['array_syntax'], []);

        /** @var ArraySyntaxFixer $arrayRule */
        $arrayRule = $rules[0];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arrayRule);
        $this->assertSame(
            'long',
            Assert::getObjectAttribute($arrayRule, 'config')
        );

        $rules = $this->fixerFactory->createFromEnabledRulesAndExcludedRules([
            'array_syntax' => [
                'syntax' => 'short'
            ]
        ], []);

        /** @var ArraySyntaxFixer $arrayRule */
        $arrayRule = $rules[0];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arrayRule);
        $this->assertSame(
            'short',
            Assert::getObjectAttribute($arrayRule, 'config')
        );
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidConfigurationException
     * @expectedExceptionMessage The rule "array_syntax_typo" was not found. Did you mean "array_syntax"?
     */
    public function testInvalid()
    {
        $this->fixerFactory->createFromEnabledRulesAndExcludedRules(['array_syntax_typo'], []);
    }

    public function provideCreateData() : array
    {
        return [
            [[], [], 0],
            [['no_whitespace_before_comma_in_array'], [], 1],
            [['declare_strict_types'], [], 1],
            [['@PSR1'], [], 2],
            [['@PSR2'], [], 24],
            [['@PSR2', 'whitespace_after_comma_in_array'], [], 25],
            [['@PSR1', '@PSR2'], [], 24]
        ];
    }
}
