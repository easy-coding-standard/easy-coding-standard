<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor\Tests;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingSniffSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\SniffNaming;
use Symplify\EasyCodingStandard\CheckerSetExtractor\SniffSetExtractor;

final class SniffSetExtractorTest extends TestCase
{
    /**
     * @var SniffSetExtractor
     */
    private $sniffSetExtractor;

    protected function setUp(): void
    {
        $this->sniffSetExtractor = new SniffSetExtractor(new SniffNaming);
    }

    public function test(): void
    {
        $psr2SniffSet = $this->sniffSetExtractor->extract('PSR2');
        $this->assertGreaterThanOrEqual(20, $psr2SniffSet);

        foreach ($psr2SniffSet as $sniff => $config) {
            $this->assertTrue(is_a($sniff, Sniff::class, true));
        }
    }

    public function testMissingSetException(): void
    {
        $this->expectException(MissingSniffSetException::class);
        $this->sniffSetExtractor->extract('Nette');
    }
//
//    public function testNormalizeName(): void
//    {
//        $firstSniffSet = $this->fixerSetExtractor->extract('PSR2');
//        $secondSniffSet = $this->fixerSetExtractor->extract('psr2');
//        $thirdSniffSet = $this->fixerSetExtractor->extract('@psr2');
//
//        $this->assertSame($firstSniffSet, $secondSniffSet);
//        $this->assertSame($firstSniffSet, $thirdSniffSet);
//
//        $firstSniffSet = $this->fixerSetExtractor->extract('SYMFONY');
//        $secondSniffSet = $this->fixerSetExtractor->extract('@symfony');
//        $this->assertSame($firstSniffSet, $secondSniffSet);
//    }
}
