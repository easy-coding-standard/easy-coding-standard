<?php

/**
 * Test double for the HTML doc generator.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Tests\Core\Generators\Fixtures;

use PHP_CodeSniffer\Generators\HTML;
class HTMLDouble extends HTML
{
    /**
     * Print the footer of the HTML page without the date or version nr to make the expectation fixtures stable.
     *
     * @return void
     */
    protected function printFooter()
    {
        echo '  <div class="tag-line">';
        echo 'Documentation generated on #REDACTED#';
        echo ' by <a href="https://github.com/PHPCSStandards/PHP_CodeSniffer">PHP_CodeSniffer #VERSION#</a>';
        echo '</div>' . \PHP_EOL;
        echo ' </body>' . \PHP_EOL;
        echo '</html>' . \PHP_EOL;
    }
    /**
     * Print the _real_ footer of the HTML page.
     *
     * @return void
     */
    public function printRealFooter()
    {
        parent::printFooter();
    }
}
