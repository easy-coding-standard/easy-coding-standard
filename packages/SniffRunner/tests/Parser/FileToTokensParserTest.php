<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Parser;

use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class FileToTokensParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FileToTokensParser
     */
    private $fileToTokensParser;

    protected function setUp(): void
    {
        $this->fileToTokensParser = $this->container->get(FileToTokensParser::class);
    }

    public function test(): void
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath(
            __DIR__ . '/FileToTokensParserSource/SimplePhpFile.php'
        );

        $this->assertCount(15, $tokens);
    }
}
