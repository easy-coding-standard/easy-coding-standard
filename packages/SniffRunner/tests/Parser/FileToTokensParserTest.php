<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\Parser\FileToTokensParser;

final class FileToTokensParserTest extends TestCase
{
    public function test()
    {
        $tokens = (new FileToTokensParser())->parseFromFilePath(
            __DIR__.'/FileToTokensParserSource/SimplePhpFile.php'
        );

        $this->assertCount(7, $tokens);
    }
}
