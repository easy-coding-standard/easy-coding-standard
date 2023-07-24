<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Indentation;

use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;
use Symplify\EasyCodingStandard\Utils\PrivatesAccessorHelper;

final class IndentationTest extends AbstractTestCase
{
    public function testSpaces(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/Source/config-with-spaces-indentation.php']);

        $indentationTypeFixer = $this->make(IndentationTypeFixer::class);
        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);

        /** @var WhitespacesFixerConfig $whitespacesFixerConfig */
        $whitespacesFixerConfig = \Symplify\EasyCodingStandard\Utils\PrivatesAccessorHelper::getPropertyValue(
            $indentationTypeFixer,
            'whitespacesConfig');

        $this->assertSame('    ', $whitespacesFixerConfig->getIndent());
        $this->assertSame("\n", $whitespacesFixerConfig->getLineEnding());
    }

    public function testTabs(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/Source/config-with-tabs-indentation.php']);

        $indentationTypeFixer = $this->make(IndentationTypeFixer::class);
        $this->assertInstanceOf(WhitespacesAwareFixerInterface::class, $indentationTypeFixer);

        /** @var WhitespacesFixerConfig $whitespacesFixerConfig */
        $whitespacesFixerConfig = PrivatesAccessorHelper::getPropertyValue(
            $indentationTypeFixer,
            'whitespacesConfig',
        );

        $this->assertSame('	', $whitespacesFixerConfig->getIndent());
        $this->assertSame("\n", $whitespacesFixerConfig->getLineEnding());
    }
}
