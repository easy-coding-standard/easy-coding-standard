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
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff' => [],
            'Symplify\CodingStandard\Sniffs\Commenting\VarPropertyCommentSniff' => [],
        ];

        $uniqueCheckers = $mutualCheckerExcluder->processCheckers($checkers);

        $this->assertCount(1, $uniqueCheckers);

        $this->assertSame([
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff' => [],
        ], $uniqueCheckers);
    }
}
