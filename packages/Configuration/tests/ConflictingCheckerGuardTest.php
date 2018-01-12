<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ConflictingCheckerGuard;
use Symplify\EasyCodingStandard\Configuration\Exception\ConflictingCheckersLoadedException;

final class ConflictingCheckerGuardTest extends TestCase
{
    /**
     * @var ConflictingCheckerGuard
     */
    private $conflictingCheckerGuard;

    protected function setUp(): void
    {
        $this->conflictingCheckerGuard = new ConflictingCheckerGuard();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFriendlyCheckers(): void
    {
        $checkers = [
            'PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer' => [],
            'PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer' => [],
        ];

        $this->conflictingCheckerGuard->processCheckers($checkers);
    }

    public function testConflictingCheckers(): void
    {
        $checkers = [
            'PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer' => [],
            'PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer' => [],
        ];

        $this->expectException(ConflictingCheckersLoadedException::class);
        $this->conflictingCheckerGuard->processCheckers($checkers);
    }
}
