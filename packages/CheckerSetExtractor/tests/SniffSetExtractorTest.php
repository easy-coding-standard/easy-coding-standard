<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor\Tests;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingSniffSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\SniffSetExtractor;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class SniffSetExtractorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var SniffSetExtractor
     */
    private $sniffSetExtractor;

    protected function setUp(): void
    {
        $this->sniffSetExtractor = $this->container->get(SniffSetExtractor::class);
    }

    public function test(): void
    {
        $psr2SniffSet = $this->sniffSetExtractor->extract('PSR2');
        $this->assertGreaterThanOrEqual(35, count($psr2SniffSet));

        foreach ($psr2SniffSet as $sniff => $config) {
            $this->assertTrue(is_a($sniff, Sniff::class, true));
        }
    }

    public function testSlevomat(): void
    {
        $slevomatSniffSet = $this->sniffSetExtractor->extract('Slevomat Coding Standard');
        $this->assertGreaterThanOrEqual(30, count($slevomatSniffSet));
    }

    public function testMissingSetException(): void
    {
        $this->expectException(MissingSniffSetException::class);
        $this->sniffSetExtractor->extract('Nette');
    }

    public function testConfiguration(): void
    {
        $psr2SniffSet = $this->sniffSetExtractor->extract('PSR2');
        $lineLengthSniffConfiguration = $psr2SniffSet[LineLengthSniff::class];

        $this->assertSame([
            'lineLimit' => 120,
            'absoluteLineLimit' => 0,
        ], $lineLengthSniffConfiguration);
    }
}
