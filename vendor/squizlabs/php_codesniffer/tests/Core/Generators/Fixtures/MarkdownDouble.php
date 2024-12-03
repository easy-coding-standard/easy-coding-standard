<?php

/**
 * Test double for the Markdown doc generator.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Generators\Fixtures;

use PHP_CodeSniffer\Generators\Markdown;
class MarkdownDouble extends Markdown
{
    /**
     * Print the markdown footer without the date or version nr to make the expectation fixtures stable.
     *
     * @return void
     */
    protected function printFooter()
    {
        echo 'Documentation generated on *REDACTED*';
        echo ' by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)' . \PHP_EOL;
    }
    /**
     * Print the _real_ footer of the markdown page.
     *
     * @return void
     */
    public function printRealFooter()
    {
        parent::printFooter();
    }
}
