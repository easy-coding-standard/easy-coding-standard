<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class IndentationTest extends TestCase
{
    public function testSpaces(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/IndentationSource/config-with-spaces-indentation.yml']
        );
        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer($container);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $spacesConfig = new WhitespacesFixerConfig('    ', PHP_EOL);
        $this->assertEquals($spacesConfig, Assert::getObjectAttribute($indentationTypeFixer, 'whitespacesConfig'));
    }

    public function testTabs(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/IndentationSource/config-with-tabs-indentation.yml']
        );
        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer($container);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $tabsConfig = new WhitespacesFixerConfig('	', PHP_EOL);
        $this->assertEquals($tabsConfig, Assert::getObjectAttribute($indentationTypeFixer, 'whitespacesConfig'));
    }

    private function getIndentationTypeFixerFromContainer(ContainerInterface $container): IndentationTypeFixer
    {
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(1, $checkers);

        return array_pop($checkers);
    }
}
