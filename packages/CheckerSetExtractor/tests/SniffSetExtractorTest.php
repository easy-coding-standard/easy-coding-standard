<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor\Tests;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingSniffSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\SniffNaming;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\XmlConfigurationExtractor;
use Symplify\EasyCodingStandard\CheckerSetExtractor\SniffSetExtractor;

final class SniffSetExtractorTest extends TestCase
{
    /**
     * @var SniffSetExtractor
     */
    private $sniffSetExtractor;

    protected function setUp(): void
    {
        $this->sniffSetExtractor = new SniffSetExtractor(new SniffNaming, new XmlConfigurationExtractor);
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
