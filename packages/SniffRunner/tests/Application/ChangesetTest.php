<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ChangesetTest extends AbstractKernelTestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/easy-coding-standard.yml']
        );

        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
    }

    public function testFileProvingNeedOfProperSupportOfChangesets(): void
    {
        $smartFileInfo = new SmartFileInfo(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets.php.inc'
        );

        $changedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertStringEqualsFile(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets-fixed.php.inc',
            $changedContent
        );
    }
}
