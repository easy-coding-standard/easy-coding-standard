<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor\Tests;

use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingFixerSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\FixerSetExtractor;

final class FixerSetExtractorTest extends TestCase
{
    /**
     * @var FixerSetExtractor
     */
    private $fixerSetExtractor;

    protected function setUp(): void
    {
        $this->fixerSetExtractor = new FixerSetExtractor;
    }

    public function test(): void
    {
        $psr2FixerSet = $this->fixerSetExtractor->extract('PSR2');
        $this->assertGreaterThanOrEqual(20, $psr2FixerSet);

        $symfonyFixerSet = $this->fixerSetExtractor->extract('symfony');
        $this->assertGreaterThanOrEqual(50, $symfonyFixerSet);
    }

    public function testFixerClassesAreReturned(): void
    {
        $symfonyFixerSet = $this->fixerSetExtractor->extract('symfony');

        $this->assertArrayHasKey(BinaryOperatorSpacesFixer::class, $symfonyFixerSet);
    }

    public function testMissingSetException(): void
    {
        $this->expectException(MissingFixerSetException::class);
        $this->fixerSetExtractor->extract('Nette');
    }

    public function testNormalizeName(): void
    {
        $firstFixerSet = $this->fixerSetExtractor->extract('PSR2');
        $secondFixerSet = $this->fixerSetExtractor->extract('psr2');
        $thirdFixerSet = $this->fixerSetExtractor->extract('@psr2');

        $this->assertSame($firstFixerSet, $secondFixerSet);
        $this->assertSame($firstFixerSet, $thirdFixerSet);

        $firstFixerSet = $this->fixerSetExtractor->extract('SYMFONY');
        $secondFixerSet = $this->fixerSetExtractor->extract('@symfony');
        $this->assertSame($firstFixerSet, $secondFixerSet);
    }
}
