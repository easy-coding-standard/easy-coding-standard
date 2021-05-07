<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileProcessorTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $initialFileContent;
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;
    protected function setUp() : void
    {
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, [__DIR__ . '/FileProcessorSource/easy-coding-standard.php']);
        $this->sniffFileProcessor = $this->getService(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor::class);
    }
    public function test() : void
    {
        $smartFileInfo = new \Symplify\SmartFileSystem\SmartFileInfo(__DIR__ . '/FileProcessorSource/SomeFile.php.inc');
        $this->initialFileContent = $smartFileInfo->getContents();
        $fixedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertNotSame($this->initialFileContent, $fixedContent);
    }
    public function testGetSniffs() : void
    {
        $sniffs = $this->sniffFileProcessor->getCheckers();
        $this->assertCount(1, $sniffs);
    }
}
