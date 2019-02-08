<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Parser;

use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileToTokensParserTest extends AbstractKernelTestCase
{
    /**
     * @var FileToTokensParser
     */
    private $fileToTokensParser;

    protected function setUp(): void
    {
        static::bootKernel(EasyCodingStandardKernel::class);

        $this->fileToTokensParser = static::$container->get(FileToTokensParser::class);
    }

    public function test(): void
    {
        $tokens = $this->fileToTokensParser->parseFromFileInfo(
            new SmartFileInfo(__DIR__ . '/FileToTokensParserSource/SimplePhpFile.php')
        );

        $this->assertCount(15, $tokens);
    }
}
