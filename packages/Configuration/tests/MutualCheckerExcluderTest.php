<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\MutualCheckerExcluder;

final class MutualCheckerExcluderTest extends TestCase
{
    public function test(): void
    {
        $mutualCheckerExcluder = new MutualCheckerExcluder();
        $checkers = [
            'PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer' => [],
            'PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff' => [],
        ];

        $uniqueCheckers = $mutualCheckerExcluder->processCheckers($checkers);

        $this->assertCount(1, $uniqueCheckers);

        $this->assertSame([
            'PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer' => [],
        ], $uniqueCheckers);
    }
}
