<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue923Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        // from "\r\n" to "\n"
        $this->doTestFiles([[__DIR__ . '/wrong/wrong923.php.inc', __DIR__ . '/fixed/fixed923.php.inc']]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config923.yml';
    }
}
