<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Tests\ValueObject;

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var File
     */
    private $file;
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    protected function setUp() : void
    {
        $this->bootKernel(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class);
        $this->errorAndDiffCollector = $this->getService(\Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector::class);
        $fileFactory = $this->getService(\Symplify\EasyCodingStandard\SniffRunner\File\FileFactory::class);
        $fileInfo = new \Symplify\SmartFileSystem\SmartFileInfo(__DIR__ . '/FileSource/SomeFile.php');
        $this->file = $fileFactory->createFromFileInfo($fileInfo);
        $this->file->processWithTokenListenersAndFileInfo([], $fileInfo);
    }
    public function testErrorDataCollector() : void
    {
        $this->file->addError('Some Error', 0, \PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class);
        $this->assertCount(0, $this->errorAndDiffCollector->getFileDiffs());
        $errors = $this->errorAndDiffCollector->getErrors();
        $this->assertCount(1, $errors);
        $onlyError = $errors[0];
        $this->assertInstanceOf(\Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError::class, $onlyError);
        $this->assertSame('Some Error', $onlyError->getMessage());
        $this->assertSame(1, $onlyError->getLine());
        $this->assertSame(\PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class, $onlyError->getCheckerClass());
    }
    public function testNotImplementedGetErrorCount() : void
    {
        $this->expectException(\Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException::class);
        $this->file->getErrorCount();
    }
    public function testNotImplementedGetErrors() : void
    {
        $this->expectException(\Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException::class);
        $this->file->getErrors();
    }
}
