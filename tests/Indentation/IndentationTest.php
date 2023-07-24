<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class IndentationTest extends AbstractTestCase
{
    private PrivatesAccessor $privatesAccessor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->privatesAccessor = new PrivatesAccessor();
    }

    public function testSpaces(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/Source/config-with-spaces-indentation.php']);

        $indentationTypeFixer = $this->make(IndentationTypeFixer::class);
        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);

        /** @var WhitespacesFixerConfig $whitespacesFixerConfig */
        $whitespacesFixerConfig = $this->privatesAccessor->getPrivatePropertyOfClass(
            $indentationTypeFixer,
            'whitespacesConfig',
            WhitespacesFixerConfig::class
        );

        $this->assertSame('    ', $whitespacesFixerConfig->getIndent());
        $this->assertSame("\n", $whitespacesFixerConfig->getLineEnding());
    }

    public function testTabs(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/Source/config-with-tabs-indentation.php']);

        $indentationTypeFixer = $this->make(IndentationTypeFixer::class);
        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);

        /** @var WhitespacesFixerConfig $whitespacesFixerConfig */
        $whitespacesFixerConfig = $this->privatesAccessor->getPrivatePropertyOfClass(
            $indentationTypeFixer,
            'whitespacesConfig',
            WhitespacesFixerConfig::class
        );

        $this->assertSame('	', $whitespacesFixerConfig->getIndent());
        $this->assertSame("\n", $whitespacesFixerConfig->getLineEnding());
    }
}
