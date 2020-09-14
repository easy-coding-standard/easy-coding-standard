<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\HeredocNowdoc;

use Iterator;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HeredocNowdoc\HeredocNowdocPHPCodeFormatter;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class HeredocNowdocPHPCodeFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var HeredocNowdocPHPCodeFormatter
     */
    private $heredocNowdocPHPCodeFormatter;

    protected function setUp(): void
    {
        self::bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->heredocNowdocPHPCodeFormatter = self::$container->get(HeredocNowdocPHPCodeFormatter::class);

        /** @var EasyCodingStandardStyle $easyCodingStandardStyle */
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfos = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $changedContent = $this->heredocNowdocPHPCodeFormatter->format(
            $inputAndExpectedFileInfos->getInputFileInfo(),
            true
        );
        $contents = $inputAndExpectedFileInfos->getExpectedFileInfo()->getContents();
        $this->assertSame($contents, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php.inc');
    }
}
