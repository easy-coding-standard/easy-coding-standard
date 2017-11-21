<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

abstract class AbstractDualRunTestCase extends AbstractFixerTestCase
{
    /**
     * @param string $expected
     * @param string $input
     */
    protected function doTest($expected, $input = null, ?SplFileInfo $file = null): void
    {
        $this->fixer = $this->createFixer();

        // natural order
        [$expected, $input] = [$input, $expected];

        // autoload files
        [$expected, $input] = $this->loadFileContents($expected, $input);

        if ($input) {
            $this->lintSource($input);
        }

        if ($file === null) {
            $file = new SplFileInfo(__FILE__);
        }

        Tokens::clearCache();
        $tokens = Tokens::fromCode($input);

        // first run
        $this->fixer->fix($file, $tokens);

        // second run
        $this->fixer->fix($file, $tokens);

        $this->assertSame($tokens->generateCode(), $expected);
    }

    /**
     * @return string[]
     */
    private function loadFileContents(string $expected, string $input): array
    {
        if (file_exists($expected)) {
            $expected = file_get_contents($expected);
        }

        if (file_exists($input)) {
            $input = file_get_contents($input);
        }

        return [$expected, $input];
    }
}
