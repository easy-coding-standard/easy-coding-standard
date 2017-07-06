<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DependencyInjection;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\FixerIsNotConfigurableException;

final class FixerServiceRegistrationTest extends TestCase
{
    public function test(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.neon'
        );

        $arraySyntaxFixer = $container->get(ArraySyntaxFixer::class);
        $this->assertSame(
            ['syntax' => 'short'],
            Assert::getObjectAttribute($arraySyntaxFixer, 'configuration')
        );

        $visibilityRequiredFixer = $container->get(VisibilityRequiredFixer::class);
        $this->assertSame(
            ['elements' => ['property']],
            Assert::getObjectAttribute($visibilityRequiredFixer, 'configuration')
        );
    }

    public function testConfigureUnconfigurableFixer(): void
    {
        $this->expectException(FixerIsNotConfigurableException::class);
        $this->expectExceptionMessage(sprintf(
            'Fixer "%s" is not configurable with configuration: {"be_strict":"yea"}.',
            StrictParamFixer::class
        ));

        (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FixerServiceRegistrationSource/non-configurable-fixer.neon'
        );
    }
}
