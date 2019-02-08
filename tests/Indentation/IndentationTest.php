<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class IndentationTest extends AbstractKernelTestCase
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
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/IndentationSource/config-with-spaces-indentation.yml']
        );

        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer(self::$container);

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
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/IndentationSource/config-with-tabs-indentation.yml']
        );

        $indentationTypeFixer = $this->getIndentationTypeFixerFromContainer(self::$container);

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
