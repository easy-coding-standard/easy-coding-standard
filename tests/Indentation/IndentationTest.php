<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class IndentationTest extends TestCase
{
    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    protected function setUp(): void
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }

    public function testSpaces(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/IndentationSource/config-with-spaces-indentation.yml']
        );
        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer($container);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $spacesConfig = new WhitespacesFixerConfig('    ', PHP_EOL);

        $fixerWhitespaceConfig = $this->privatesAccessor->getPrivateProperty(
            $indentationTypeFixer,
            'whitespacesConfig'
        );
        $this->assertEquals($spacesConfig, $fixerWhitespaceConfig);
    }

    public function testTabs(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/IndentationSource/config-with-tabs-indentation.yml']
        );
        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer($container);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $tabsConfig = new WhitespacesFixerConfig('	', PHP_EOL);

        $fixerWhitespaceConfig = $this->privatesAccessor->getPrivateProperty(
            $indentationTypeFixer,
            'whitespacesConfig'
        );
        $this->assertEquals($tabsConfig, $fixerWhitespaceConfig);
    }

    private function getIndentationTypeFixerFromContainer(ContainerInterface $container): IndentationTypeFixer
    {
        $fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $checkers = $fixerFileProcessor->getCheckers();
        $this->assertCount(1, $checkers);

        return array_pop($checkers);
    }
}
