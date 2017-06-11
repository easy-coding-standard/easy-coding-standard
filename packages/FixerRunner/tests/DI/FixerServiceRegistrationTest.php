<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\DI;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Tests\ContainerFactoryWithCustomConfig;

final class FixerServiceRegistrationTest extends TestCase
{
    /**
     * @var ArraySyntaxFixer
     */
    private $arraySyntaxFixer;

    protected function setUp(): void
    {
        $container = (new ContainerFactoryWithCustomConfig)->createWithConfig(
            __DIR__ . '/FixerServiceRegistrationSource/easy-coding-standard.neon'
        );

        $this->arraySyntaxFixer = $container->get(ArraySyntaxFixer::class);
    }

    public function test(): void
    {
        $this->assertSame(
            ['syntax' => 'short'],
            Assert::getObjectAttribute($this->arraySyntaxFixer, 'configuration')
        );
    }
}
