<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class IndentationTest extends AbstractKernelTestCase
{
    private PrivatesAccessor $privatesAccessor;

    protected function setUp(): void
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }

    public function testSpaces(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/IndentationSource/config-with-spaces-indentation.php']
        );

        $indentationTypeFixer = $this->getService(IndentationTypeFixer::class);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $whitespacesFixerConfig = new WhitespacesFixerConfig('    ', "\n");

        $fixerWhitespaceConfig = $this->privatesAccessor->getPrivatePropertyOfClass(
            $indentationTypeFixer,
            'whitespacesConfig',
            WhitespacesFixerConfig::class
        );
        $this->assertEquals($whitespacesFixerConfig, $fixerWhitespaceConfig);
    }

    public function testTabs(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/IndentationSource/config-with-tabs-indentation.php']
        );

        $indentationTypeFixer = $this->getService(IndentationTypeFixer::class);

        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);
        $whitespacesFixerConfig = new WhitespacesFixerConfig('	', "\n");

        $fixerWhitespaceConfig = $this->privatesAccessor->getPrivatePropertyOfClass(
            $indentationTypeFixer,
            'whitespacesConfig',
            WhitespacesFixerConfig::class
        );
        $this->assertEquals($whitespacesFixerConfig, $fixerWhitespaceConfig);
    }
}
