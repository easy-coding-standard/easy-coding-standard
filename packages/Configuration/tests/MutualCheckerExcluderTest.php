<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Test;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\MutualCheckerExcluder;

final class MutualCheckerExcluderTest extends TestCase
{
    /**
     * @var MutualCheckerExcluder
     */
    private $mutualCheckerExcluder;

    protected function setUp(): void
    {
        $this->mutualCheckerExcluder = new MutualCheckerExcluder;
    }

    public function test(): void
    {
        $checkers = [
            'SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff',
            'Symplify\CodingStandard\Sniffs\Commenting\VarPropertyCommentSniff',
        ];

        $uniqueCheckers = $this->mutualCheckerExcluder->exclude($checkers);
        $this->assertCount(1, $uniqueCheckers);

        $this->assertSame(['SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff'], $uniqueCheckers);
    }
}
