<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
final class ChangesetTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;
    protected function setUp() : void
    {
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, [__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/easy-coding-standard.php']);
        $this->sniffFileProcessor = $this->getService(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor::class);
    }
    public function testFileProvingNeedOfProperSupportOfChangesets() : void
    {
        $smartFileInfo = new \Symplify\SmartFileSystem\SmartFileInfo(__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProveNeedSupportChangesets.php.inc');
        $changedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertStringEqualsFile(__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProveNeedSupportChangesets-fix.php.inc', $changedContent);
    }
}
