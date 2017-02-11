<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\PhpCsFixer\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
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
    public function testCreateFromRulesAndExcludedRules(array $rules, array $excludedRules, int $expectedFixerCount)
    {
        $rules = $this->fixerFactory->createFromRulesAndExcludedRules($rules, $excludedRules);
        $this->assertCount($expectedFixerCount, $rules);

        if (count($rules)) {
            $fixer = $rules[0];
            $this->assertInstanceOf(FixerInterface::class, $fixer);
        }
    }

    public function testRuleConfiguration()
    {
        // @todo: e.g. array => short
    }

    public function provideCreateData() : array
    {
        return [
            [[], [], 0],
            [['no_whitespace_before_comma_in_array'], [], 1],
            [['declare_strict_types,'], [], 1],
            [['@PSR1'], [], 2],
            [['@PSR2'], [], 24],
            [['@PSR2', 'whitespace_after_comma_in_array'], [], 25],
            [['@PSR1', '@PSR2'], [], 24]
        ];
    }
}
