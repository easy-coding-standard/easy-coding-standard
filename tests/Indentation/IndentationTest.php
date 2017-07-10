<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

final class IndentationTest extends TestCase
{
    public function testSpaces(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/IndentationSource/config-with-spaces-indentation.neon'
        );

        /** @var IndentationTypeFixer $indentationFixer */
        $indentationFixer = $container->get(IndentationTypeFixer::class);

        $spacesConfig = new WhitespacesFixerConfig('    ', PHP_EOL);
        $this->assertEquals($spacesConfig, Assert::getObjectAttribute($indentationFixer, 'whitespacesConfig'));
    }

    public function testTabs(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/IndentationSource/config-with-tabs-indentation.neon'
        );

        /** @var IndentationTypeFixer $indentationFixer */
        $indentationFixer = $container->get(IndentationTypeFixer::class);

        $tabsConfig = new WhitespacesFixerConfig('	', PHP_EOL);
        $this->assertEquals($tabsConfig, Assert::getObjectAttribute($indentationFixer, 'whitespacesConfig'));
    }
}
