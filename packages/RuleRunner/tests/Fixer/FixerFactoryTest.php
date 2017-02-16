<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Tests\Fixer;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\RuleRunner\Fixer\FixerFactory;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FixerFactoryTest extends TestCase
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $this->fixerFactory = $container->getByType(FixerFactory::class);
    }

    public function testRuleConfiguration()
    {
        $rules = $this->fixerFactory->createFromFixerClasses([ArraySyntaxFixer::class]);

        /** @var ArraySyntaxFixer $arrayRule */
        $arrayRule = $rules[0];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arrayRule);
        $this->assertSame(
            'long',
            Assert::getObjectAttribute($arrayRule, 'config')
        );

        $rules = $this->fixerFactory->createFromFixerClasses([
            ArraySyntaxFixer::class => [
                'syntax' => 'short'
            ]
        ]);

        /** @var ArraySyntaxFixer $arrayRule */
        $arrayRule = $rules[0];
        $this->assertInstanceOf(ArraySyntaxFixer::class, $arrayRule);
        $this->assertSame(
            'short',
            Assert::getObjectAttribute($arrayRule, 'config')
        );
    }
}
