<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue816Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        // each file has different line ending
        $this->assertFileNotEquals(__DIR__ . '/wrong/wrong816.php.inc', __DIR__ . '/wrong/wrong816.2.php.inc');

        $this->doTestFiles([
            // from "\r\n" to "\r\n"
            [__DIR__ . '/wrong/wrong816.php.inc', __DIR__ . '/fixed/fixed816.php.inc'],
            // from "\n" to "\r\n"
            [__DIR__ . '/wrong/wrong816.2.php.inc', __DIR__ . '/fixed/fixed816.php.inc'],
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config816.yml';
    }
}
