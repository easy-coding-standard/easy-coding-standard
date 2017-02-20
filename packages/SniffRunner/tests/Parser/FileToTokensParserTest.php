<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Parser\FileToTokensParser;

final class FileToTokensParserTest extends TestCase
{
    public function test()
    {
        $tokens = (new FileToTokensParser)->parseFromFilePath(
            __DIR__.'/FileToTokensParserSource/SimplePhpFile.php'
        );

        $this->assertCount(9, $tokens);
    }
}
